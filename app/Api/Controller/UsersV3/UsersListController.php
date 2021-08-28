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
use App\Models\GroupUser;
use App\Models\User;
use App\Models\UserFollow;
use App\Repositories\UserRepository;
use App\Models\Group;
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
        $column = '';
        !empty($filter['username']) && $column = 'username';
        !empty($filter['nickname']) && $column = 'nickname';
        if (empty($column)) {
            $query = User::query();
            $query->where('users.status', User::STATUS_NORMAL);
            if (isset($filter['hot']) && $filter['hot'] == 1) {
                $query->orderByDesc('users.login_at');
            } else {
                $query->orderBy('users.id');
            }
        } else {
            $value = $filter[$column];
            $query = User::query()->where($column, $value)->where('status', User::STATUS_NORMAL);
            $likeL = User::query()->where($column, 'like', $value . '%')->where('status', User::STATUS_NORMAL);
            $likeR = User::query()->where($column, 'like', '%' . $value)->where('status', User::STATUS_NORMAL);
            $likeM = User::query()->where($column, 'like', '%' . $value . '%')->where('status', User::STATUS_NORMAL);
            $query->union($likeL->getQuery())->union($likeR->getQuery())->union($likeM->getQuery());
        }
        $users = $this->pagination($currentPage, $perPage, $query);
        $userDatas = $users['pageData'];
        $userIds = array_column($userDatas, 'id');
        $groupUsers = GroupUser::query()->whereIn('user_id', $userIds)->get()->toArray();
        $groupUsers = array_column($groupUsers, null, 'user_id');
        $groupIds = array_column($groupUsers, 'group_id');
        $userGroupDatas = Group::query()->whereIn('id', $groupIds)->where('is_display', 1)->get()->pluck(null, 'id')->toArray();
        $userFollowList = UserFollow::query()->where('from_user_id', $this->user->id)->get()->pluck(null, 'to_user_id')->toArray();
        // 将来需考虑单用户-多权限组情况
        $res = [];
        foreach ($userDatas as $userData) {
            $userId = $userData['id'];
            $groupId = $groupUsers[$userId]['group_id'] ?? '';
            $item = [
                'userId'=>$userId,
                'nickname' => $userData['nickname'],
                'username' => $userData['username'],
                'avatar' => $userData['avatar'],
                'threadCount' => $userData['thread_count'],
                'questionCount' => $userData['question_count'],
                'likedCount' => $userData['liked_count'],
                'followCount' => $userData['follow_count'],
                'isFollow' => false,
                'isMutualFollow' => false,
                'groupName' => $userGroupDatas[$groupId]['name'] ?? ''
            ];
            if (isset($userFollowList[$userId])) {
                $item['isFollow'] = true;
                $item['isMutualFollow'] = (bool)$userFollowList[$userId]['is_mutual'];
            }
            $res[] = $item;
        }
        $users['pageData'] = $res;
        return $this->outPut(ResponseCode::SUCCESS, '', $users);
    }
}
