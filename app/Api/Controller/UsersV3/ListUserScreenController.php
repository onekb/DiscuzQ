<?php
/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Api\Controller\UsersV3;

use App\Common\ResponseCode;
use App\Models\DenyUser;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Models\Group;
use App\Models\UserSignInFields;
use Illuminate\Support\Arr;
use Discuz\Base\DzqController;
use Illuminate\Support\Str;

class ListUserScreenController extends DzqController
{
    // 权限检查，是否为管理员
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $userRepo->canListUserScren($this->user);
    }


    public function main()
    {
        $actor = $this->user;
        $currentPage = $this->inPut('page');
        $perPage = $this->inPut('perPage');
        $filter = (array)$this->inPut('filter');

        $query = User::query();
        $query->select('users.id AS userId', 'users.expired_at','users.nickname','users.mobile', 'users.username', 'users.avatar', 'users.thread_count', 'users.status', 'users.created_at', 'users.updated_at', 'group_id','expiration_time');
        $query->join('group_user', 'users.id', '=', 'group_user.user_id');
        $query->orderBy('created_at');

        if (Arr::has($filter, 'username') && Arr::get($filter, 'username') !== '') {
            $username = $filter['username'];
            // 用户名前后存在星号（*）则使用模糊查询
            if(strpos($username,',') !== false){
                $usernames = explode(',',$username);
                $query->whereIn('users.username', $usernames);
                foreach ($usernames as $un){
                    if (Str::startsWith($un, '*') || Str::endsWith($un, '*')) {
                        $un = Str::replaceLast('*', '%', Str::replaceFirst('*', '%', $un));
                        $query->orWhere('username', 'like', $un);
                    }
                }
            }else{
                if (Str::startsWith($username, '*') || Str::endsWith($username, '*')) {
                    $username = Str::replaceLast('*', '%', Str::replaceFirst('*', '%', $username));
                    $query->orWhere('username', 'like', $username);
                }else{
                    $query->where('users.username', 'like','%'.$username.'%');
                }
            }
        }

        if (Arr::has($filter, 'nickname') && Arr::get($filter, 'nickname') !== '') {
            $nickname = $filter['nickname'];
            $query->where('users.nickname', 'like', '%' . $nickname . '%');
        }
        //用户id
        if ($ids = Arr::get($filter, 'id')) {
            $ids = explode(',', $ids);
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        // 手机号
        if (Arr::has($filter, 'mobile')) {
            $mobile = $filter['mobile'];
            $query->where('users.mobile', $mobile);
        }

        // 状态
        if (Arr::has($filter, 'status') && Arr::get($filter, 'status') !== '') {
            $status = $filter['status'];
            $query->where('users.status', $status);
        }

        // 用户组
        if ($group_id = Arr::get($filter, 'groupId')) {
              $query  ->whereIn('group_id', $group_id);
        }
        // 是否实名认证
        if ($isReal = Arr::get($filter, 'isReal')) {
            if ($isReal == 'yes') {
                $query->where('realname', '<>', '');
            } elseif ($isReal == 'no') {
                $query->where('realname', '');
            }
        }


       // 是否绑定微信
        if ($weChat = Arr::get($filter, 'wechat')) {
            if ($weChat === 'yes') {
                $query->has('wechat');
            } elseif ($weChat === 'no') {
                $query->doesntHave('wechat');
            }
        }

       // 是否已lahei
        if ($deny = Arr::get($filter, 'deny')) {
            if ($deny === 'yes') {
                $query->addSelect([
                    'denyStatus' => DenyUser::query()
                        ->select('user_id')
                        ->where('user_id', $actor->id)
                        ->whereRaw('deny_user_id = id')
                        ->limit(1)
                ]);
            }
        }

        $users = $this->pagination($currentPage, $perPage, $query,false);
        $userDatas = $users['pageData'];
        $userDatasArr = $users['pageData']->toArray();
        $userIds = array_column($userDatasArr, 'userId');
        $ordersRegisterPaid = Order::query()->whereIn('user_id',$userIds)
                                ->where('type',Order::ORDER_TYPE_REGISTER)
                                ->where('status',Order::ORDER_STATUS_PAID)
                                ->distinct(true)
                                ->get()
                                ->keyBy('user_id')
                                ->toArray();
        $userDatas = $userDatas->map(function (User $user) use ($ordersRegisterPaid){
            $user->paid = true;
            if (!($user->group_id == Group::ADMINISTRATOR_ID)) {
                $siteMode = Setting::getValue('site_mode');
                //过期时间非空，有付费订单
                if (!empty($user->expired_at) && !empty($ordersRegisterPaid[$user->userId])) {
                    $t1 = strtotime($user->expired_at);
                    $t2 = time();
                    $diffTime = abs($t1 - $t2);
                    if ($diffTime >= 3600 && $t1 < $t2) {
                        $user->paid = false;
                        //兜底逻辑,防止异常情况下判断错误
                    }
                }else{
                    $user->paid = false;
                }
            }
            return $user;
        });
        $userDatas = $userDatas->toArray();

        $groupIds = array_column($userDatas, 'group_id');

        $userGroupDatas = Group::query()->whereIn('id', $groupIds)->where('is_display', 1)->get()->toArray();
        $userGroupDatas = array_column($userGroupDatas, null, 'id');

        $result = [];
        foreach ($userDatas as  $value) {
            $result[] = [
                'userId' => $value['userId'],
                'username' => $value['username'],
                'nickname' => $value['nickname'],
                'mobile' => $value['mobile'],
                'avatarUrl' => $value['avatar'],
                'expiredAt' => $value['expired_at'],
                'threadCount' => $value['thread_count'],
                'status' => $value['status'],
                'createdAt' => $value['created_at'],
                'updatedAt' => $value['updated_at'],
                'groupName' => $userGroupDatas[$value['group_id']]['name'] ?? '',
                'expirationTime' =>$value['expiration_time'],
                'extFields' =>  UserSignInFields::instance()->getUserSignInFields($value['userId']),
                'paid'=>$value['paid']
            ];
        }

        $userDatas = $this->camelData($result);
        $users['pageData'] = $userDatas ?? [];

        return $this->outPut(ResponseCode::SUCCESS, '', $users);
    }

}
