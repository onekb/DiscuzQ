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

namespace App\Api\Controller\WalletV3;

use App\Common\ResponseCode;
use App\Models\UserWalletCash;
use App\Models\UserWechat;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class UsersCashLogsListController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $currentPage = $this->inPut('page');
        $perPage = $this->inPut('perPage');
        $filter = (array)$this->inPut('filter');

        $query = UserWalletCash::query();
        $query->select('user_wallet_cash.*', 'users.nickname');
        $query->join('users', 'user_wallet_cash.user_id', '=', 'users.id');
        if (isset($filter['nickname']) && !empty($filter['nickname'])) {
            $query->where('users.nickname', 'like', '%' . $filter['nickname'] . '%');
        }

        if (isset($filter['cashStatus']) && !empty($filter['cashStatus'])) {
            $query->where('user_wallet_cash.cash_status', $filter['cashStatus']);
        }

        if (isset($filter['cashSn']) && !empty($filter['cashSn'])) {
            $cashSn = (int) $filter['cashSn'];
            $query->where('user_wallet_cash.cash_sn', $cashSn);
        }

        if (isset($filter['startTime']) && !empty($filter['startTime'])) {
            $query->where('user_wallet_cash.created_at', '>=', $filter['startTime']);
        }

        if (isset($filter['endTime']) && !empty($filter['endTime'])) {
            $query->where('user_wallet_cash.created_at', '<=', $filter['endTime']);
        }

        $query->orderByDesc('user_wallet_cash.created_at');
        $usersWalletCashLogs = $this->pagination($currentPage, $perPage, $query);

        $userIds = array_column($usersWalletCashLogs['pageData'], 'user_id');
        $userWechatData = UserWechat::query()->whereIn('user_id', $userIds)->get()->toArray();
        $userWechatData = array_column($userWechatData, null, 'user_id');

        foreach ($usersWalletCashLogs['pageData'] as $key => $value) {
            $usersWalletCashLogs['pageData'][$key]['cash_sn'] = (string) $value['cash_sn'];
            $usersWalletCashLogs['pageData'][$key]['wechat']['mp_openid'] = $userWechatData[$value['user_id']]['mp_openid'] ?? "";
            $usersWalletCashLogs['pageData'][$key]['wechat']['min_openid'] = $userWechatData[$value['user_id']]['min_openid'] ?? "";
        }
        $usersWalletCashLogs['pageData'] = $this->camelData($usersWalletCashLogs['pageData']) ?? [];

        return $this->outPut(ResponseCode::SUCCESS, '', $usersWalletCashLogs);
    }
}