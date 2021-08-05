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

use App\Models\Order;
use App\Models\OrderChildren;
use App\Models\RedPacket;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use App\Notifications\ReceiveRedPacket;
use Discuz\Base\DzqLog;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Database\ConnectionInterface;

class RedPacketExpireCommand extends AbstractCommand
{
    protected $signature = 'redPacket:expire';

    protected $description = '返还过期未领取完的红包金额';

    protected $expireTime = 24 * 60 * 60; //红包过期时间24小时

    protected $app;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    protected $info = '';

    /**
     * AvatarCleanCommand constructor.
     * @param string|null $name
     * @param Application $app
     * @param ConnectionInterface $connection
     */
    public function __construct(string $name = null, Application $app, ConnectionInterface $connection)
    {
        parent::__construct($name);

        $this->app = $app;
        $this->connection = $connection;
    }

    public function handle()
    {
        $this->info('红包过期脚本执行 [开始]');
        $this->info('');

        // 定时任务处理此条记录的时间，与用户最后参与领红包的时间增加 10 秒，以防时间临界点并发引起问题
        $compareTime = date("Y-m-d H:i:s", time() - $this->expireTime + 10);
        $query = RedPacket::query();
        $query->where('created_at', '<', $compareTime);
        $query->where('remain_money', '>', 0);
        $query->where('remain_number', '>', 0);
        $query->where('status', '=', RedPacket::RED_PACKET_STATUS_VALID); // 1:红包未过期
        $redPacket = $query->get();
        $orderType = [Order::ORDER_TYPE_MERGE, Order::ORDER_TYPE_TEXT, Order::ORDER_TYPE_LONG, Order::ORDER_TYPE_REDPACKET];
        $orderStatus = [Order::ORDER_STATUS_PAID, Order::ORDER_STATUS_PART_OF_RETURN];

        $bar = $this->createProgressBar(count($redPacket));
        $bar->start();

        $redPacket->map(function ($item) use ($bar, $orderType, $orderStatus) {
            // Start Transaction
            $this->connection->beginTransaction();
            try {
                $order = Order::query()
                    ->where('thread_id', $item->thread_id)
                    ->whereIn('status', $orderStatus)
                    ->whereIn('type', $orderType)
                    ->first();
                if (empty($order)) {
                    app('log')->info('获取不到该红包帖订单信息，无法处理剩余红包金额！;红包帖ID为：' . $item->thread_id . '，红包附属信息ID为：' . $item->id);
                    $item->remain_money = 0;
                    $item->remain_number = 0;
                    $item->status = RedPacket::RED_PACKET_STATUS_UNTREATED;
                    $item->save();
                    $this->connection->commit();
                    return;
                }

                $userWallet = UserWallet::query()->where('user_id', $order->user_id)->first();
                if ($order->payment_type == Order::PAYMENT_TYPE_WALLET && ($userWallet->freeze_amount < $item->remain_money)) {
                    app('log')->info('过期红包返回错误：红包帖(ID为' . $item->thread_id . ')，作者(ID为' . $item->user_id . ')。异常错误记录：冻结金额 - 剩余金额 <= 0，无法返回');
                    $item->status = RedPacket::RED_PACKET_STATUS_UNTREATED;
                    $item->remain_money = 0;
                    $item->remain_number = 0;
                    $item->save;

                    $order->status = Order::ORDER_STATUS_UNTREATED;
                    $order->save();
                    $this->connection->commit();
                    return;
                }

                // $postRedLog-已发放的红包流水记录
                if ($order->type == Order::ORDER_TYPE_TEXT) {
                    $postRedLog = UserWalletLog::query()
                        ->where(['thread_id' => $item->thread_id, 'change_type' => UserWalletLog::TYPE_INCOME_TEXT])
                        ->get()->toArray();
                    $changeType = UserWalletLog::TYPE_TEXT_RETURN_THAW;// 103 文字帖冻结返还
                    $changeDesc = trans('wallet.return_text');
                } elseif ($order->type == Order::ORDER_TYPE_LONG) {
                    $postRedLog = UserWalletLog::query()
                        ->where(['thread_id' => $item->thread_id, 'change_type' => UserWalletLog::TYPE_INCOME_LONG])
                        ->get()->toArray();
                    $changeType = UserWalletLog::TYPE_LONG_RETURN_THAW;// 113 长文帖冻结返还
                    $changeDesc = trans('wallet.return_long');
                } else {
                    $postRedLog = UserWalletLog::query()
                       ->where(['thread_id' => $item->thread_id, 'change_type' => UserWalletLog::TYPE_REDPACKET_INCOME])
                        ->get()->toArray();
                    $changeType = UserWalletLog::TYPE_REDPACKET_REFUND;// 170 合并订单退款
                    $changeDesc = trans('wallet.redpacket_refund');
                }

                $remainMoney = floatval(sprintf('%.2f', $item->remain_money));
                // 已发放的红包金额(红包表数值对比)
                $rewardTotal = $item->money - $item->remain_money;
                if (!empty($postRedLog)) {
                    $rewardTotal = array_sum(array_column($postRedLog, 'change_available_amount'));
                    // 已发放的红包金额(钱包流水数值对比)
                    if ($item->money > ($order->amount - $rewardTotal)) {
                        $remainMoney = $order->amount - $rewardTotal;
                        app('log')->info('过期红包返回异常记录：红包帖(ID为' . $item->thread_id . ')，作者(ID为' . $item->user_id . ')。异常记录：应返回的剩余金额与流水统计比较后的数值不一致，以较小的数值' . $remainMoney . '返回');
                    }
                }

                if ($order->refund + $remainMoney == $order->amount) {
                    $orderChangeStatus = Order::ORDER_STATUS_RETURN;
                } else {
                    $orderChangeStatus = Order::ORDER_STATUS_PART_OF_RETURN;
                }

                if ($remainMoney == $item->money) {
                    $orderChildrenChangeStatus = Order::ORDER_STATUS_RETURN;
                } else {
                    $orderChildrenChangeStatus = Order::ORDER_STATUS_PART_OF_RETURN;
                }

                if ($order->payment_type == Order::PAYMENT_TYPE_WALLET) {
                    $userWalletUpdateResult = UserWallet::query()->where('user_id', $order->user_id)
                        ->update(['available_amount' => $userWallet->available_amount + $remainMoney, 
                                  'freeze_amount' => $userWallet->freeze_amount - $remainMoney]);
                    $changeFreezeAmount = $remainMoney;
                } else {
                    $userWalletUpdateResult = UserWallet::query()->where('user_id', $order->user_id)
                        ->update(['available_amount' => $userWallet->available_amount + $remainMoney]);
                }

                $trueChangeFreezeAmount = $changeFreezeAmount ? -$changeFreezeAmount : 0;
                UserWalletLog::createWalletLog(
                    $order->user_id,
                    $remainMoney,
                    $trueChangeFreezeAmount,
                    $changeType,
                    $changeDesc,
                    null,
                    null,
                    $order->user_id,
                    0,
                    0,
                    $item->thread_id
                );

                if ($order->type == Order::ORDER_TYPE_MERGE) {
                    $orderChildrenInfo = OrderChildren::query()
                        ->where('type', Order::ORDER_TYPE_REDPACKET)
                        ->where('status', Order::ORDER_STATUS_PAID)
                        ->where('order_sn', $order->order_sn)
                        ->where('thread_id', $order->thread_id)
                        ->first();
                    if (!empty($orderChildrenInfo)) {
                        $orderChildrenInfo->refund = $remainMoney;
                        $orderChildrenInfo->status = $orderChildrenChangeStatus;
                        $orderChildrenInfo->return_at = Carbon::now();
                        $orderChildrenInfo->save();
                    }
                }

                $order->refund = $order->refund + $remainMoney;
                $order->status = $orderChangeStatus;
                $order->return_at = Carbon::now();
                $order->save();

                // 发送通知
                $user = User::query()->where('id', $order->user_id)->first();
                if (empty($user)) {
                    app('log')->info('发送红包过期通知失败：红包帖(ID为' . $item->thread_id . ')，作者(ID为' . $item->user_id . ')。异常错误记录：作者信息不存在，无法发送通知');
                } else {
                    if (empty($order->thread)) {
                        $message = '红包帖已过期且已被删除，返回剩余冻结金额';
                    } else {
                        $message = $order->thread->getContentByType(Thread::CONTENT_LENGTH, true);
                    }
                    $build = [
                        'message' => $message,
                        'raw' => array_merge(Arr::only($order->toArray(), ['id', 'thread_id', 'type']), [
                            'actor_username' => $user->username,   // 发送人姓名
                            'actual_amount' => $remainMoney,     // 获取作者实际金额
                        ]),
                    ];
                    // Tag 发送得到红包通知
                    $user->notify(new ReceiveRedPacket($user, $order, $build));
                }

                $item->remain_number = 0;
                $item->remain_money = 0;
                $item->status = RedPacket::RED_PACKET_STATUS_RETURN;
                $item->save();
                $this->connection->commit();
            } catch (Exception $e) {
                DzqLog::error('redPacket_expire_refund_failure', [], $e->getMessage());
                $this->connection->rollback();
            }

            $bar->advance();
        });

        $bar->finish();

        $this->info('');
        $this->info('红包过期脚本执行 [结束]');
    }
}
