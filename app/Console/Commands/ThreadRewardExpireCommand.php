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
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use App\Models\Order;
use Carbon\Carbon;
use App\Repositories\ThreadRewardRepository;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
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
     * @var Dispatcher
     */
    protected $bus;

    /**
     * AvatarCleanCommand constructor.
     * @param string|null $name
     * @param Application $app
     * @param ConnectionInterface $connection
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
        // 定时任务处理此条记录的时间，与用户最后参与领红包的时间增加 10 秒，以防时间临界点并发引起问题
        $now = Carbon::now()->addSeconds(10)->toDateTimeString();
        $query = ThreadReward::query();
        $query->where('type', 0);
        $query->where('expired_at', '<', $now);
        $query->where('remain_money', '>', 0); // 还有剩余金额
        $threadReward = $query->get();

        $bar = $this->createProgressBar(count($threadReward));
        $bar->start();

        $threadReward->map(function ($item) use ($bar) {
            $item->remain_money = floatval(sprintf('%.2f', $item->remain_money));
            $userWallet = UserWallet::query()->lockForUpdate()->find($item->user_id);
            $threadRewardOrder = Order::query()->where(['thread_id' => $item->thread_id, 'status' => Order::ORDER_STATUS_PAID])->first();

            if(empty($threadRewardOrder)){
                app('log')->info('获取不到悬赏帖订单信息，无法处理剩余悬赏金额！;悬赏问答帖ID为：' . $item->thread_id);
            }else{
                if($threadRewardOrder['payment_type'] == Order::PAYMENT_TYPE_WALLET && ($userWallet->freeze_amount - $item->remain_money < 0)){
                    app('log')->info('过期悬赏错误：悬赏帖(ID为' . $item->thread_id . ')，作者(ID为' . $item->user_id . ')，钱包冻结金额 小于 应返回的悬赏剩余金额，悬赏剩余金额返回失败！');
                }else{
                    $this->connection->beginTransaction();
                    try{
                        $change_freeze_amount = 0;
                        $remain_money = $item->remain_money;

                        // 通过订单实付金额、用户钱包流水统计实际已悬赏的金额，获取真实的剩余金额
                        $postRewardLog = UserWalletLog::query()->where('thread_id', $item->thread_id)->get()->toArray();
                        // 已悬赏金额
                        $rewardTotal = $item->money - $item->remain_money;
                        if(!empty($postRewardLog)){
                            $rewardTotal = array_sum(array_column($postRewardLog, 'change_available_amount'));
                        }
                        // 实际应返回 = 实付 - 实际已悬赏金额
                        $trueRemainMoney = $threadRewardOrder['amount'] - $rewardTotal;
                        if($trueRemainMoney <= 0){
                            app('log')->info('过期悬赏返回错误：悬赏帖(ID为' . $item->thread_id . ')，作者(ID为' . $item->user_id . ')。异常错误记录：剩余金额 <= 0，无法返回');
                        }else{
                            if($trueRemainMoney !== $item->remain_money){
                                $remain_money = $trueRemainMoney;
                            }
                            if($threadRewardOrder['payment_type'] == Order::PAYMENT_TYPE_WALLET){
                                $userWallet->freeze_amount = $userWallet->freeze_amount - $remain_money;
                                $change_freeze_amount = $remain_money;
                            }

                            $userWallet->available_amount = $userWallet->available_amount + $remain_money;
                            $userWallet->save();

                            UserWalletLog::createWalletLog(
                                $item->user_id,
                                $remain_money,
                                -$change_freeze_amount,
                                UserWalletLog::TYPE_INCOME_THREAD_REWARD_RETURN,
                                trans('wallet.income_thread_reward_return_desc'),
                                null,
                                null,
                                $item->user_id,
                                0,
                                0,
                                $item->thread_id
                            );

                            // 发送悬赏问答通知
                            app(ThreadRewardRepository::class)->returnThreadRewardNotify($item->thread_id, $item->user_id, $remain_money, UserWalletLog::TYPE_INCOME_THREAD_REWARD_RETURN);
                        }

                        $item->remain_money = 0;
                        $item->save();
                        $this->connection->commit();

                    }catch (Exception $e) {
                        app('log')->info('过期悬赏返回错误：悬赏帖(ID为' . $item->thread_id . ')，作者(ID为' . $item->user_id . ')。异常错误记录：' . $e->getMessage());
                        $this->connection->rollback();
                    }
                }
            }

            $bar->advance();
        });

        $bar->finish();
    }
}
