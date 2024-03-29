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

namespace App\Console\Commands;

use App\Models\Finance;
use App\Models\Order;
use App\Models\UserWalletCash;
use Carbon\Carbon;
use Discuz\Console\AbstractCommand;
use Discuz\Qcloud\QcloudStatisticsTrait;

class FinanceCreateCommand extends AbstractCommand
{
    use QcloudStatisticsTrait;
    protected $signature = 'finance:create';

    protected $description = 'Count the financial situation of the previous day.';

    protected $finance;

    protected $order;

    protected $userWalletCash;

    public function __construct(Finance $finance, Order $order, UserWalletCash $userWalletCash)
    {
        $this->finance = $finance;
        $this->order = $order;
        $this->userWalletCash = $userWalletCash;
        parent::__construct();
    }

    public function handle()
    {
        $this->postStatisData();
        $date = Carbon::parse('-1 day')->toDateString();
        $dateTimeBegin = $date . ' 00:00:00';
        $dateTimeEnd = $date . ' 23:59:59';
        $orderType = [$this->order::ORDER_STATUS_PAID, $this->order::ORDER_STATUS_PART_OF_RETURN, 
                      $this->order::ORDER_STATUS_RETURN, $this->order::ORDER_STATUS_UNTREATED];

        $register_profit = $this->order::WhereBetween('created_at', [$dateTimeBegin, $dateTimeEnd])->where('type', 1)->where('status', $this->order::ORDER_STATUS_PAID)->sum('amount');
        $master_portion = $this->order::WhereBetween('created_at', [$dateTimeBegin, $dateTimeEnd])->where('status', $this->order::ORDER_STATUS_PAID)->sum('master_amount');
        $withdrawal_profit = $this->userWalletCash::WhereBetween('created_at', [$dateTimeBegin, $dateTimeEnd])->where('cash_status', $this->userWalletCash::STATUS_PAID)->sum('cash_charge');
        $amount = $this->order::WhereBetween('created_at', [$dateTimeBegin, $dateTimeEnd])->whereIn('status', $orderType)->sum('amount');
        $refund_amount = $this->order::WhereBetween('created_at', [$dateTimeBegin, $dateTimeEnd])->whereIn('status', $orderType)->sum('refund');
        $order_amount = $amount - $refund_amount; // 总收入-总退款 = 实际收入
        $order_count = $this->order::WhereBetween('created_at', [$dateTimeBegin, $dateTimeEnd])->count();
        $withdrawal = $this->userWalletCash::WhereBetween('created_at', [$dateTimeBegin, $dateTimeEnd])->where('cash_status', $this->userWalletCash::STATUS_PAID)->sum('cash_apply_amount');

        $this->finance->updateOrCreate(
            ['created_at' => $date],
            [
                'income' => $order_amount,
                'withdrawal' => $withdrawal,
                'order_count' => $order_count,
                'order_amount' => $order_amount,
                'total_profit' => $register_profit + $master_portion + $withdrawal_profit,
                'register_profit' => $register_profit,
                'master_portion' => $master_portion,
                'withdrawal_profit' => $withdrawal_profit
            ]
        );
        $this->info($date . ' ' . $this->signature);
    }
}
