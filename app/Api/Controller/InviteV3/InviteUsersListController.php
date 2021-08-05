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

namespace App\Api\Controller\InviteV3;

use App\Common\ResponseCode;
use App\Models\UserDistribution;
use App\Models\GroupUser;
use App\Models\User;
use App\Models\Order;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class InviteUsersListController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $currentPage = $this->inPut('page');
        $perPage = $this->inPut('perPage');

        $query = UserDistribution::query();
        $query->select('user_distributions.pid', 'user_distributions.user_id', 'users.avatar', 'users.joined_at');
        $query->join('users', 'users.id', '=', 'user_distributions.user_id');
        $query->where('user_distributions.pid', $this->user->id);

        // 总邀请人数
        $totalInviteUsers = $query->count();

        $inviteUsersList = $this->pagination($currentPage, $perPage, $query);
        $inviteData = $inviteUsersList['pageData'] ?? [];
        $userIds = array_column($inviteData, 'user_id');
        $users = User::instance()->getUsers($userIds);
        $users = array_column($users, null, 'id');

        $registOrderDatas = Order::query()
            ->whereIn('user_id', $userIds)
            ->where(['type' => Order::ORDER_TYPE_REGISTER, 'status' => Order::ORDER_STATUS_PAID])
            ->get()->toArray();
        $registOrderDatas = array_column($registOrderDatas, null, 'user_id');
        foreach ($inviteData as $key => $value) {
            $inviteData[$key]['nickname'] = $users[$value['user_id']]['nickname'] ?? '';
            $inviteData[$key]['bounty'] = 0;
            if (isset($registOrderDatas[$value['user_id']])) {
                $inviteData[$key]['bounty'] = floatval($registOrderDatas[$value['user_id']]['third_party_amount']);
            }
        }

        $groups = GroupUser::instance()->getGroupInfo([$this->user->id]);
        $groups = array_column($groups, null, 'user_id');

        // 总邀请赏金
        $totalInviteBounties = Order::query()
            ->where(['type' => Order::ORDER_TYPE_REGISTER, 'status' => Order::ORDER_STATUS_PAID, 'third_party_id' => $this->user->id])
            ->sum('third_party_amount');

        $result = array(
            'userId' => $this->user->id,
            'nickname' => $this->user->nickname,
            'avatar' => $this->user->avatar,
            'groupName' => $groups[$this->user->id]['groups']['name'],
            'totalInviteUsers' => $totalInviteUsers,
            'totalInviteBounties' => $totalInviteBounties,
            'inviteUsersList' => $inviteData
        );
        $inviteUsersList['pageData'] = $result;
        return $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($inviteUsersList));
    }
}
