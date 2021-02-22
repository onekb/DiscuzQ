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

use App\Commands\Wallet\ChangeUserWallet;
use App\Models\Order;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\ConnectionInterface;

class AbnormalOrderDealCommand extends AbstractCommand
{
    protected $signature = 'abnormalOrder:clear';

    protected $description = '处理问答提问支付、文字帖、长文贴红包的异常订单，返还金额给用户';

    // 清除 10 分钟之前的异常订单，避免帖子创建时间过长误清除了订单
    protected $expireTime = 10 * 60;

    // 要处理的订单类型
    protected $orderType = [
        Order::ORDER_TYPE_QUESTION,
        Order::ORDER_TYPE_TEXT,
        Order::ORDER_TYPE_LONG
    ];

    protected $app;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var Dispatcher
     */
    protected $bus;

    protected $debugInfo = true;

    protected $info = '';

    /**
     * AvatarCleanCommand constructor.
     * @param string|null $name
     * @param Application $app
     * @param ConnectionInterface $connection
     * @param Dispatcher $bus
     */
    public function __construct(string $name = null, Application $app, ConnectionInterface $connection, Dispatcher $bus)
    {
        parent::__construct($name);

        $this->app = $app;
        $this->connection = $connection;
        $this->bus = $bus;
    }

    public function handle()
    {
        $this->info('脚本执行 [开始]');
        $this->info('');

        $preTime = time() - $this->expireTime;
        $compareTime = date("Y-m-d H:i:s",$preTime);
        $query = Order::query();
        $query->where('created_at', '<', $compareTime);
        $query->where('status', '=', 1); // 订单状态：0待付款；1已付款；2.取消订单；3支付失败；4订单过期；10已退款；11不处理订单
        $query->where('amount', '>', 0);
//        $query->where('payment_type', '=', Order::PAYMENT_TYPE_WALLET); // 20钱包支付
        $query->where('thread_id', null);
        $query->whereIn('type',$this->orderType); // 交易类型 5问答提问支付 20文字帖红包 21长文帖红包
        $order = $query->get();

        $bar = $this->createProgressBar(count($order));
        $bar->start();
        $this->info('');

        $order->map(function ($item) use ($bar) {
            $this->info = '异常订单ID：'.$item->id
                .', 用户ID:' . $item->user_id
                .', 退还金额:' . $item->amount
                .', ';

            if ($item->type == Order::ORDER_TYPE_TEXT) {
                $change_type = UserWalletLog::TYPE_TEXT_ABNORMAL_REFUND;// 104 文字帖订单异常返现
                $change_desc = trans('wallet.abnormal_return_text');
            } elseif ($item->type == Order::ORDER_TYPE_LONG) {
                $change_type = UserWalletLog::TYPE_LONG_ABNORMAL_REFUND;// 114 长文帖订单异常返现
                $change_desc = trans('wallet.abnormal_return_long');
            } elseif ($item->type == Order::ORDER_TYPE_QUESTION) {
                $change_type = UserWalletLog::TYPE_QUESTION_ABNORMAL_REFUND;// 124 问答帖订单异常返现
                $change_desc = trans('wallet.abnormal_return_question');
            } else {
                $this->changeOrderStatus($item->id, Order::ORDER_STATUS_UNTREATED);
                $this->outDebugInfo('订单金额退还失败, 订单类型: ' . $item->type . ', 不在处理范围内');
                $this->connection->commit();
                return;
            }

            $userWallet = UserWallet::query()->where('user_id', $item->user_id)->first();
            if (!empty($userWallet)) {
                $userWallet = $userWallet->toArray();
            } else {
                // 改变订单状态 11:在异常订单处理中不进行处理的订单
                $this->changeOrderStatus($item->id, Order::ORDER_STATUS_UNTREATED);
                $this->outDebugInfo('订单金额退还失败, 用户钱包异常');
                $this->connection->commit();
                return;
            }

            if ($item->payment_type == Order::PAYMENT_TYPE_WALLET) {
                if ($userWallet['freeze_amount'] < $item->amount) {
                    // 改变订单状态 11:在异常订单处理中不进行处理的订单
                    $this->changeOrderStatus($item->id, Order::ORDER_STATUS_UNTREATED);
                    $this->outDebugInfo('订单金额退还失败, ' . '用户冻结金额:' . $userWallet['freeze_amount'] . ' < '.'退还金额:' . $item->amount);
                    $this->connection->commit();
                    return;
                }
            }

            $debugInfo =    '原可用金额：' . $userWallet['available_amount']
                        . ', 原冻结金额：' . $userWallet['freeze_amount']
            ;

            $data = [
                'order_id'      => $item->id,
                'thread_id'     => $item->thread_id ? $item->thread_id : 0,
                'post_id'       => $item->post_id ? $item->post_id : 0,
                'change_type'   => $change_type,
                'change_desc'   => $change_desc
            ];

            // Start Transaction
            $this->connection->beginTransaction();
            try {
                if ($item->payment_type == Order::PAYMENT_TYPE_WALLET) {
                    // 钱包支付 减少冻结金额，增加可用金额
                    $this->bus->dispatch(new ChangeUserWallet($item->user,
                                                              UserWallet::OPERATE_UNFREEZE,
                                                              $item->amount,
                                                              $data
                                         ));
                } elseif (
                    $item->payment_type     == Order::PAYMENT_TYPE_WECHAT_NATIVE
                    || $item->payment_type  == Order::PAYMENT_TYPE_WECHAT_WAP
                    || $item->payment_type  == Order::PAYMENT_TYPE_WECHAT_JS
                    || $item->payment_type  == Order::PAYMENT_TYPE_WECHAT_MINI
                ) {
                    // 其余支付类型 增加可用金额
                    $this->bus->dispatch(new ChangeUserWallet($item->user,
                                                              UserWallet::OPERATE_INCREASE,
                                                              $item->amount,
                                                              $data
                                         ));
                } else {
                    $this->changeOrderStatus($item->id, Order::ORDER_STATUS_UNTREATED);
                    $this->outDebugInfo('订单金额退还失败, 订单支付类型: ' . $item->payment_type . ', 不在处理范围内');
                    $this->connection->commit();
                    return;
                }

                $userWallet = UserWallet::query()->where('user_id', $item->user_id)->first();
                if (!empty($userWallet)) {
                    $userWallet = $userWallet->toArray();
                } else {
                    $this->changeOrderStatus($item->id, Order::ORDER_STATUS_UNTREATED);
                    $this->outDebugInfo('订单金额退还失败, 用户钱包异常');
                    $this->connection->commit();
                    return;
                }

                $this->changeOrderStatus($item->id, Order::ORDER_STATUS_RETURN); // 改变订单状态 10:已退款订单
                $this->outDebugInfo($debugInfo
                                    . ', 现可用金额：' . $userWallet['available_amount']
                                    . ', 现冻结金额：' . $userWallet['freeze_amount']
                                    . ', 订单金额退还成功'
                );
                $this->connection->commit();
            } catch (Exception $e) {
                $this->outDebugInfo('订单金额退还失败: ' . $e->getMessage());
                $this->connection->rollback();
            }

            $bar->advance();
            $this->info('');
        });

        $bar->finish();

        $this->info('');
        $this->info('脚本执行 [完成]');
    }

    public function changeOrderStatus($id ,$status){
        $order = Order::query()->lockForUpdate()->find($id);
        $order->status = $status;
        $order->save();
    }

    public function outDebugInfo($info){
        $this->info($this->info . $info);
        app('log')->info($this->info . $info);
    }
}
