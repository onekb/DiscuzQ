<?php
/**
 * Copyright (C) 2021 Tencent Cloud.
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


use App\Api\Serializer\UserProfileSerializer;
use App\Api\Serializer\UserV2Serializer;
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\Dialog;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserWechat;
use App\Repositories\UserRepository;
use App\Settings\SettingsRepository;
use Carbon\Carbon;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Database\Eloquent\Collection;

class ProfileController extends DzqController
{

    public $providers = [
        \App\Providers\UserServiceProvider::class,
    ];

    //返回的数据一定包含的数据
    public $include = [
    ];

    public $optionalInclude = ['groups', 'dialog'];

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $user_serialize = $this->app->make(UserV2Serializer::class);
        $user_serialize->setRequest($this->request);
        $user_id = $this->inPut('pid');
        $user = User::find($user_id);
        if(!$user){
            return $this->outPut(ResponseCode::INVALID_PARAMETER);
        }
        $isSelf = $this->user->id === $user->id;
        if($isSelf || $this->user->isAdmin()){
            $this->optionalInclude = array_merge($this->optionalInclude, ['wechat']);
        }
        $include = !empty($this->inPut('include')) ? array_unique(array_merge($this->include, explode(',', $this->inPut('include')))) : $this->include;

        if(!empty($this->inPut('include'))){
            if(array_diff($this->inPut('include'), $this->optionalInclude)){       //如果include 超出optionalinclude 就报错
                return $this->outPut(ResponseCode::NET_ERROR);
            }
        }

        // 付费模式是否过期 true：已付费，false：未付费
        $userRepo = app(UserRepository::class);
        $user->paid = $userRepo->isPaid($user);

        //isRenew：表示用户是否续过费，true：已续过费，false：未续过费
        $user->isRenew = false;
        $order = Order::query()->where([
            'status' => Order::ORDER_STATUS_PAID,
            'user_id' => $user->id
        ])  ->whereIn('type', [Order::ORDER_TYPE_REGISTER, Order::ORDER_TYPE_RENEW])
            ->exists();
        if (!empty($order)) {
            $user->isRenew = true;
        }

        $key = array_search('dialog', $include);
        if($key != false){
            if(!$isSelf){
                $actor = $this->user;
                //添加会话关系
                $dialog = Dialog::query()
                    ->where(['sender_user_id' => $actor->id, 'recipient_user_id' => $user->id])
                    ->orWhere(function ($query) use ($actor, $user) {
                        $query->where(['sender_user_id' => $user->id, 'recipient_user_id' => $actor->id]);
                    })
                    ->first();
                $user->setRelation('dialog', $dialog);
            }else{
                unset($include[$key]);
            }
        }
        $user->loadMissing($include);

        // 判断用户是否禁用
        if($user->status == User::enumStatus('ban')){
            $user->load(['latelyLog' => function ($query) {
                $query->select()->where('action', 'ban');
            }]);
        }

        //如果是当前用户，计算出审核中和已忽略的数量
        if ($user_id == $this->user->id) {
            $user->thread_count = Thread::query()
                ->where('user_id', $user_id)
                ->whereNull('deleted_at')
                ->where('is_draft',Thread::IS_NOT_DRAFT)
                ->count();
        }

        $data = $user_serialize->getDefaultAttributes($user);
        $grounUser = [$user_id];
        $groups = GroupUser::instance()->getGroupInfo($grounUser);
        $groups = array_column($groups, null, 'user_id');
        $data['group'] = $this->getGroupInfo($groups[$user_id]);

        //用户是否绑定微信
        $data['isBindWechat'] = !empty($user->wechat);
        $data['wxNickname'] = !empty($user->wechat) ? $user->wechat->nickname : '';
        $data['wxHeadImgUrl'] = !empty($user->wechat) ? $user->wechat->headimgurl : '';

        return $this->outPut(ResponseCode::SUCCESS,'', $data);

    }


    /**
     * @param $cacheData
     * @param null $limit
     * @return Collection
     */
    public function search($limit, $cacheData = null)
    {
        $query = User::query()->selectRaw('id,username,avatar,liked_count as likedCount')
            ->where('status', 0)
            ->whereBetween('login_at', [Carbon::parse('-30 days'), Carbon::now()])
            ->orderBy('thread_count', 'desc')
            ->orderBy('login_at', 'desc');
        // cache
        if ($cacheData) {
            $query->whereIn('id', $cacheData);
        }

        return $query->take($limit)->get();
    }

    private function getGroupInfo($group)
    {
        return [
            'pid' => $group['group_id'],
            'groupName' => $group['groups']['name']
        ];
    }



}
