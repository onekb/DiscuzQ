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

use App\Models\ThreadReward;
use App\Models\Post;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use App\Models\Order;
use App\Models\OrderChildren;
use App\Repositories\ThreadRewardRepository;
use Discuz\Base\DzqLog;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\ConnectionInterface;

class ThreadRewardExpireCommand extends AbstractCommand
{
    protected $signature = 'reward:expire';

    protected $description = '分配过期的剩余悬赏金额';

    protected $app;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

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
        $this->info('悬赏过期脚本执行 [开始]');
        $this->info('');

        // 定时任务处理此条记录的时间，与用户最后参与领红包的时间增加 10 秒，以防时间临界点并发引起问题
        $now = Carbon::now()->addSeconds(10)->toDateTimeString();
        $query = ThreadReward::query();
        $query->where('type', 0);
        $query->where('expired_at', '<', $now);
        $query->where('remain_money', '>', 0); // 还有剩余金额
        $threadReward = $query->get();
        $orderType = [Order::ORDER_TYPE_QUESTION, Order::ORDER_TYPE_QUESTION_REWARD, Order::ORDER_TYPE_MERGE];
        $orderStatus = [Order::ORDER_STATUS_PAID, Order::ORDER_STATUS_PART_OF_RETURN];

        $bar = $this->createProgressBar(count($threadReward));
        $bar->start();

        $threadReward->map(function ($item) use ($bar, $orderType, $orderStatus) {
            $this->connection->beginTransaction();
            try {
                $order = Order::query()
                    ->where(['thread_id' => $item->thread_id, 'user_id' => $item->user_id])
                    ->whereIn('status', $orderStatus)
                    ->whereIn('type', $orderType)
                    ->first();
                if (empty($order)) {
                    app('log')->info('获取不到悬赏帖订单信息，无法处理剩余悬赏金额！;悬赏问答帖ID为：' . $item->thread_id . '，悬赏附属信息ID为：' . $item->id);
                    $item->remain_money = 0;
                    $item->save();
                    $this->connection->commit();
                    return;
                }

                $userWallet = UserWallet::query()->lockForUpdate()->find($item->user_id);
                if ($order->payment_type == Order::PAYMENT_TYPE_WALLET && ($userWallet->freeze_amount < $item->remain_money)) {
                    app('log')->info('过期悬赏错误：悬赏帖(ID为' . $item->thread_id . ')，作者(ID为' . $item->user_id . ')，钱包冻结金额 小于 应返回的悬赏剩余金额，悬赏剩余金额返回失败！');
                    $item->remain_money = 0;
                    $item->save();

                    $order->status = Order::ORDER_STATUS_UNTREATED;
                    $order->save();
                    $this->connection->commit();
                    return;
                }

                // $postRewardLog-已发放的悬赏流水记录
                if ($order->type == Order::ORDER_TYPE_QUESTION) {
                    $postRewardLog = UserWalletLog::query()
                        ->where(['thread_id' => $item->thread_id, 'change_type' => UserWalletLog::TYPE_INCOME_THREAD_REWARD])
                        ->get()->toArray();
                    $changeType = UserWalletLog::TYPE_INCOME_THREAD_REWARD_RETURN;// 121 悬赏帖过期-悬赏帖剩余悬赏金额返回
                    $changeDesc = trans('wallet.income_thread_reward_return_desc');
                } else {
                    $postRewardLog = UserWalletLog::query()
                        ->where(['thread_id' => $item->thread_id, 'change_type' => UserWalletLog::TYPE_QUESTION_REWARD_INCOME])
                        ->get()->toArray();
                    $changeType = UserWalletLog::TYPE_QUESTION_REWARD_REFUND;// 162 悬赏问答退款
                    $changeDesc = trans('wallet.question_reward_refund');
                }

                $remainMoney = floatval(sprintf('%.2f', $item->remain_money));
                // 已发放的悬赏金额(悬赏表数值对比)
                $rewardTotal = $item->money - $item->remain_money;
                if (!empty($postRewardLog)) {
                    $rewardTotal = array_sum(array_column($postRewardLog, 'change_available_amount'));
                    // 已发放的红包金额(钱包流水数值对比)
                    if ($item->money > ($order->amount - $rewardTotal)) {
                        $remainMoney = $order->amount - $rewardTotal;
                        app('log')->info('过期悬赏返回异常记录：红包帖(ID为' . $item->thread_id . ')，作者(ID为' . $item->user_id . ')。异常记录：应返回的剩余金额与流水统计比较后的数值不一致，以较小的数值' . $remainMoney . '返回');
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
                    $userWalletUpdateResult = UserWallet::query()->where('user_id', $item->user_id)
                        ->update(['available_amount' => $userWallet->available_amount + $remainMoney, 
                                  'freeze_amount' => $userWallet->freeze_amount - $remainMoney]);
                } else {
                    $userWalletUpdateResult = UserWallet::query()->where('user_id', $item->user_id)
                        ->update(['available_amount' => $userWallet->available_amount + $remainMoney]);
                }

                if ($order->payment_type == Order::PAYMENT_TYPE_WALLET) {
                    // 减少冻结金额
                    UserWalletLog::createWalletLog(
                        $order->user_id,
                        0,
                        -$remainMoney,
                        UserWalletLog::TYPE_QUESTION_REWARD_FREEZE_RETURN,
                        trans('wallet.question_reward_freeze_return'),
                        null,
                        null,
                        0,
                        0,
                        0,
                        $item->thread_id
                    );
                }

                // 增加钱包余额
                UserWalletLog::createWalletLog(
                    $order->user_id,
                    $remainMoney,
                    0,
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
                        ->where('type', Order::ORDER_TYPE_QUESTION_REWARD)
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
                    app('log')->info('发送悬赏过期通知失败：悬赏帖(ID为' . $item->thread_id . ')，作者(ID为' . $item->user_id . ')。异常错误记录：作者信息不存在，无法发送通知');
                } else {
                    app(ThreadRewardRepository::class)->returnThreadRewardNotify($item->thread_id, $item->user_id, $remainMoney, UserWalletLog::TYPE_INCOME_THREAD_REWARD_RETURN);
                }

                $item->remain_money = 0;
                $item->save();
                $this->connection->commit();
            } catch (Exception $e) {
                DzqLog::error('threadReward_expire_refund_failure', [], $e->getMessage());
                $this->connection->rollback();
            }

            $bar->advance();
        });

        $bar->finish();
        $this->info('');
        $this->info('悬赏过期脚本执行 [结束]');
        
    }
}
