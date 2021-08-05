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

namespace App\Traits;

use App\Models\DenyUser;
use App\Models\Group;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait UserTrait
{
    /**
     * user list filters.
     *
     * @param Builder $query
     * @param array $filter
     * @param User|null $actor
     */
    private function applyFilters(Builder $query, $filter, User $actor = null)
    {
        // 多个/单个 用户id
        if ($ids = Arr::get($filter, 'id')) {
            $ids = explode(',', $ids);
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        // 用户名
        if ($username = Arr::get($filter, 'username')) {
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

        // 昵称搜索
        if ($nickname = Arr::get($filter, 'nickname')) {
            $query->where('users.nickname', 'like', '%' . $nickname . '%');
        }

        // 手机号
        if ($mobile = Arr::get($filter, 'mobile')) {
            $query->where('mobile', $mobile);
        }

        // 状态
        if (Arr::has($filter, 'status') && Arr::get($filter, 'status') !== '') {
            $status = $filter['status'];
            $query->where('users.status', $status);
        }

        // 用户组
        if ($group_id = Arr::get($filter, 'group_id')) {
            $query->join('group_user', 'users.id', '=', 'group_user.user_id')
                ->whereIn('group_id', $group_id);
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

        // 是否已
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

        // 是否可以被提问
        if ($canBeAsked = Arr::get($filter, 'canBeAsked')) {
            $groupIds = Permission::query()
                ->where('permission', 'canBeAsked')
                ->pluck('group_id')
                ->add(Group::ADMINISTRATOR_ID);

            $query->join('group_user', 'group_user.user_id', '=', 'users.id')
                ->where('user_id', '<>', $actor->id);

            if ($canBeAsked === 'yes') {
                $query->whereIn('group_user.group_id', $groupIds);
            } elseif ($canBeAsked === 'no') {
                $query->whereNotIn('group_user.group_id', $groupIds);
            }
        }
    }
}
