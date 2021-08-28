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

namespace App\Api\Controller\StatisticV3;

use App\Common\ResponseCode;
use App\Models\Order;
use App\Models\UserWallet;
use App\Models\UserWalletCash;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Support\Arr;

class FinanceProfileController extends DzqController
{
    /**
     * @var Order
     */
    private $order;
    /**
     * @var UserWallet
     */
    private $userWallet;
    /**
     * @var UserWalletCash
     */
    private $userWalletCash;

    /**
     * @param Order $order
     * @param UserWallet $userWallet
     * @param UserWalletCash $userWalletCash
     */
    public function __construct(Order $order, UserWallet $userWallet, UserWalletCash $userWalletCash)
    {
        $this->order = $order;
        $this->userWallet = $userWallet;
        $this->userWalletCash = $userWalletCash;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $financeProfile = call_user_func([$this, '__invoke']);

        return $this->outPut(ResponseCode::SUCCESS, '', $financeProfile);
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        $financeProfile = [];
        //用户总充值
        data_set(
            $financeProfile,
            'totalIncome',
            round($this->order::where('status', $this->order::ORDER_STATUS_PAID)->sum('amount'), 2)
        );
        //用户总提现
        data_set(
            $financeProfile,
            'totalWithdrawal',
            round($this->userWalletCash::where('cash_status', $this->userWalletCash::STATUS_PAID)->sum('cash_apply_amount'), 2)
        );
        //用户钱包总金额
        $userWallet = $this->userWallet::selectRaw('SUM(available_amount) as available_amount')
            ->selectRaw('SUM(freeze_amount) as freeze_amount')
            ->first()
            ->toArray();
        data_set(
            $financeProfile,
            'totalWallet',
            round($userWallet['available_amount'] + $userWallet['freeze_amount'], 2)
        );
        //提现手续费收入
        data_set(
            $financeProfile,
            'withdrawalProfit',
            round($this->userWalletCash::where('cash_status', $this->userWalletCash::STATUS_PAID)->sum('cash_charge'), 2)
        );
        //打赏提成收入
        data_set(
            $financeProfile,
            'orderRoyalty',
            round($this->order::where('status', $this->order::ORDER_STATUS_PAID)->sum('master_amount'), 2)
        );
        //注册加入收入
        data_set(
            $financeProfile,
            'totalRegisterProfit',
            round($this->order::where('type', $this->order::ORDER_TYPE_REGISTER)->where('status', $this->order::ORDER_STATUS_PAID)->sum('amount'), 2)
        );
        //平台总盈利：注册加入收入+打赏提成收入+提现手续费收入
        data_set(
            $financeProfile,
            'totalProfit',
            round($financeProfile['totalRegisterProfit'] + $financeProfile['orderRoyalty'] + $financeProfile['withdrawalProfit'], 2)
        );
        //用户订单总数
        data_set(
            $financeProfile,
            'orderCount',
            $this->order::count()
        );

        return $financeProfile;
    }
}
