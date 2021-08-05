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
use App\Models\OrderChildren;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\ConnectionInterface;

class AbnormalOrderDealCommand extends AbstractCommand
{
    protected $signature = 'abnormalOrder:clear';

    protected $description = '处理问答提问支付、红包、悬赏的异常订单，返还金额给用户';

    // 清除 15 分钟之前的异常订单，避免帖子创建时间过长误清除了订单
    protected $expireTime = 15 * 60;

    protected $app;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var Dispatcher
     */
    protected $bus;

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
        $this->info('异常订单脚本执行 [开始]');
        $this->info('');

        $orderType = [
            Order::ORDER_TYPE_QUESTION, Order::ORDER_TYPE_REDPACKET,
            Order::ORDER_TYPE_QUESTION_REWARD, Order::ORDER_TYPE_MERGE,
            Order::ORDER_TYPE_TEXT, Order::ORDER_TYPE_LONG,
        ];

        $date = Carbon::parse('-1 day')->toDateString();
        $dateTimeBegin = $date . ' 00:00:00';
        $preTime = time() - $this->expireTime;
        $dateTimeEnd = date("Y-m-d H:i:s", $preTime); // 筛选昨天至当前15分钟前的异常订单
        $query = Order::query();
        $query->whereBetween('created_at', [$dateTimeBegin, $dateTimeEnd]);
        $query->where('status', Order::ORDER_STATUS_PAID);
        $query->where('amount', '>', 0);
        $query->whereIn('type', $orderType);
        $query->where(function ($query){
            $query->whereNull('thread_id')->orWhere('thread_id', 0);
        });

        $order = $query->get();
        $bar = $this->createProgressBar(count($order));
        $bar->start();
        $this->info('');

        $order->map(function ($item) use ($bar) {
            if ($item->type == Order::ORDER_TYPE_TEXT) {
                $changeType = UserWalletLog::TYPE_TEXT_ABNORMAL_REFUND;// 104 文字帖订单异常返现
                $changeDesc = trans('wallet.abnormal_return_text');
            } elseif ($item->type == Order::ORDER_TYPE_LONG) {
                $changeType = UserWalletLog::TYPE_LONG_ABNORMAL_REFUND;// 114 长文帖订单异常返现
                $changeDesc = trans('wallet.abnormal_return_long');
            } elseif ($item->type == Order::ORDER_TYPE_QUESTION) {
                $changeType = UserWalletLog::TYPE_QUESTION_ABNORMAL_REFUND;// 124 问答帖订单异常返现
                $changeDesc = trans('wallet.abnormal_return_question');
            } elseif ($item->type == Order::ORDER_TYPE_REDPACKET) {
                $changeType = UserWalletLog::TYPE_REDPACKET_ORDER_ABNORMAL_REFUND;// 154 红包订单异常退款
                $changeDesc = trans('wallet.redpacket_order_abnormal_refund');
            } elseif ($item->type == Order::ORDER_TYPE_QUESTION_REWARD) {
                $changeType = UserWalletLog::TYPE_QUESTION_ORDER_ABNORMAL_REFUND;// 163 悬赏订单异常退款
                $changeDesc = trans('wallet.question_order_abnormal_refund');
            } elseif ($item->type == Order::ORDER_TYPE_MERGE) {
                //合并订单支付 包含  红包 + 悬赏，这里需要拆分成两条钱包流水记录，
                $changeType = $changeDesc = $merge_amount = [];
                //获取 order_children 子订单中红包、悬赏金额
                $order_children = OrderChildren::query()->where('order_sn', $item->order_sn)->get(['type', 'amount'])->toArray();
                foreach ($order_children as $val){
                    switch ($val['type']){
                        case OrderChildren::TYPE_REDPACKET:
                            $changeType[] = UserWalletLog::TYPE_REDPACKET_ORDER_ABNORMAL_REFUND;       // 红包订单异常退款，154
                            $changeDesc[] = trans('wallet.redpacket_order_abnormal_refund');
                            $merge_amount[] = $val['amount'];
                            break;
                        case OrderChildren::TYPE_REWARD:
                            $changeType[] = UserWalletLog::TYPE_QUESTION_ORDER_ABNORMAL_REFUND;       // 悬赏订单异常退款，163
                            $changeDesc[] = trans('wallet.question_order_abnormal_refund');
                            $merge_amount[] = $val['amount'];
                            break;
                        default:
                            break;
                    }
                }
            }

            $userWallet = UserWallet::query()->where('user_id', $item->user_id)->first();
            if (empty($userWallet)) {
                app('log')->info('未获取到订单创建者的钱包信息，无法处理订单金额！;订单号为：' . $item->order_sn . '，订单创建者ID为：' . $item->user_id);
                $item->status = Order::ORDER_STATUS_UNTREATED;
                $item->save();
                $this->connection->commit();
                return;
            }

            if ($item->payment_type == Order::PAYMENT_TYPE_WALLET && $userWallet->freeze_amount < $item->amount) {
                app('log')->info('用户冻结金额小于订单金额，无法退还订单金额！订单号为：' . $item->order_sn . ';订单创建者ID为：' . $item->user_id . ';用户冻结金额:' . $userWallet->freeze_amount . ';应退还金额:' . $item->amount);
                $item->status = Order::ORDER_STATUS_UNTREATED;
                $item->save();
                $this->connection->commit();
                return;
            }

            $data = [
                'order_id' => $item->id,
                'thread_id' => $item->thread_id ? $item->thread_id : 0,
                'post_id' => $item->post_id ? $item->post_id : 0,
                'change_type' => $changeType,
                'change_desc' => $changeDesc,
            ];

            // Start Transaction
            $this->connection->beginTransaction();
            try {
                if ($item->payment_type == Order::PAYMENT_TYPE_WALLET) {
                    //如果是合并订单的话，需要分多笔增加余额
                    if($item->type == Order::ORDER_TYPE_MERGE && !empty($data['change_type']) && !empty($merge_amount)){
                        foreach ($data['change_type'] as $k => $v){
                            $m_data = [
                                'order_id' => $data['order_id'],
                                'thread_id' => $data['thread_id'],
                                'post_id' => $data['post_id'],
                                'change_type'   =>  $v,
                                'change_desc'   =>  $data['change_desc'][$k]
                            ];
                            $this->bus->dispatch(new ChangeUserWallet($item->user,
                                UserWallet::OPERATE_UNFREEZE,
                                $merge_amount[$k],
                                $m_data
                            ));
                        }
                    }else{
                        // 钱包支付 减少冻结金额，增加可用金额
                        $this->bus->dispatch(new ChangeUserWallet($item->user,
                            UserWallet::OPERATE_UNFREEZE,
                            $item->amount,
                            $data
                        ));
                    }
                } elseif (
                    $item->payment_type == Order::PAYMENT_TYPE_WECHAT_NATIVE
                    || $item->payment_type == Order::PAYMENT_TYPE_WECHAT_WAP
                    || $item->payment_type == Order::PAYMENT_TYPE_WECHAT_JS
                    || $item->payment_type == Order::PAYMENT_TYPE_WECHAT_MINI
                ) {
                    if($item->type == Order::ORDER_TYPE_MERGE && !empty($data['change_type']) && !empty($merge_amount)){
                        foreach ($data['change_type'] as $k => $v){
                            $m_data = [
                                'order_id' => $data['order_id'],
                                'thread_id' => $data['thread_id'],
                                'post_id' => $data['post_id'],
                                'change_type'   =>  $v,
                                'change_desc'   =>  $data['change_desc'][$k]
                            ];
                            $this->bus->dispatch(new ChangeUserWallet($item->user,
                                UserWallet::OPERATE_INCREASE,
                                $merge_amount[$k],
                                $m_data
                            ));
                        }
                    }else{
                        $this->bus->dispatch(new ChangeUserWallet($item->user,
                            UserWallet::OPERATE_INCREASE,
                            $item->amount,
                            $data
                        ));
                    }
                } else {
                    app('log')->info('订单金额退还失败, 订单号 ' . $item->order_sn . '的支付类型: ' . $item->payment_type . ', 不在处理范围内');
                    $item->status = Order::ORDER_STATUS_UNTREATED;
                    $item->save();
                    $this->connection->commit();
                    return;
                }

                if ($item->type == Order::ORDER_TYPE_MERGE) {
                    $orderChildrenInfo = OrderChildren::query()
                        ->where('status', Order::ORDER_STATUS_PAID)
                        ->where('order_sn', $item->order_sn)
                        ->whereNull('thread_id')
                        ->orWhere('thread_id', 0)
                        ->get();
                    $orderChildrenInfo->map(function ($child) {
                        $child->refund = $child->amount;
                        $child->status = Order::ORDER_STATUS_RETURN;
                        $child->return_at = Carbon::now();
                        $child->save();
                    });
                }
                $item->status = Order::ORDER_STATUS_RETURN;
                $item->refund = $item->amount;
                $item->return_at = Carbon::now();
                $item->save();
                $this->connection->commit();
            } catch (Exception $e) {
                app('log')->info('订单金额退还失败, 订单号 ' . $item->order_sn . '异常抛出: ' . $e->getMessage());
                $this->connection->rollback();
            }

            $bar->advance();
            $this->info('');
        });

        $bar->finish();

        $this->info('');
        $this->info('异常订单脚本执行 [完成]');
    }
}
