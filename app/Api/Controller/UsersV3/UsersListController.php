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
use App\Models\User;
use App\Models\UserFollow;
use App\Repositories\UserRepository;
use App\Models\Group;
use Illuminate\Support\Arr;
use Discuz\Base\DzqController;

class UsersListController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $currentPage = $this->inPut('page');
        $perPage = $this->inPut('perPage');
        $filter = (array)$this->inPut('filter');

        $query = User::query();
        $query->select('users.id AS userId', 'users.nickname', 'users.username', 'users.avatar', 'users.thread_count', 'users.question_count', 'users.liked_count', 'users.follow_count', 'group_id');
        $query->join('group_user', 'users.id', '=', 'group_user.user_id');
        $query->where('users.status', User::STATUS_NORMAL);
        if (Arr::has($filter, 'username') && Arr::get($filter, 'username') !== '') {
            $username = $filter['username'];
            $query->where('users.username', 'like', '%' . $username . '%');
        }
        if (Arr::has($filter, 'nickname') && Arr::get($filter, 'nickname') !== '') {
            $nickname = $filter['nickname'];
            $query->where('users.nickname', 'like', '%' . $nickname . '%');
        }

        if (isset($filter['hot']) && $filter['hot'] == 1) {
            $query->orderByDesc('users.login_at');
        } else {
            $query->orderBy('users.id');
        }


        $users = $this->pagination($currentPage, $perPage, $query);
        $userDatas = $users['pageData'];
        $groupIds = array_column($userDatas, 'group_id');
        $userGroupDatas = Group::query()->whereIn('id', $groupIds)->where('is_display', 1)->get()->toArray();
        $userGroupDatas = array_column($userGroupDatas, null, 'id');

        $userFollowList = UserFollow::query()->where('from_user_id', $this->user->id)->get()->toArray();
        $userFollowList = array_column($userFollowList, null, 'to_user_id');

        // 将来需考虑单用户-多权限组情况
        foreach ($userDatas as $key => $value) {
            $userDatas[$key]['nickname']       = $value['nickname'] ? $value['nickname'] : $value['username'];
            $userDatas[$key]['isFollow']       = false;
            $userDatas[$key]['isMutualFollow'] = false;
            if (isset($userFollowList[$value['userId']])) {
                $userDatas[$key]['isFollow']       = true;
                $userDatas[$key]['isMutualFollow'] = (bool) $userFollowList[$value['userId']]['is_mutual'];
            }
            $userDatas[$key]['groupName'] = $userGroupDatas[$value['group_id']]['name'] ?? '';
            unset($userDatas[$key]['group_id']);
        }
        $userDatas = $this->camelData($userDatas);
        $users['pageData'] = $userDatas ?? [];

        return $this->outPut(ResponseCode::SUCCESS, '', $users);
    }
}
