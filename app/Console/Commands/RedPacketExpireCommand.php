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
use App\Models\RedPacket;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\ConnectionInterface;

class RedPacketExpireCommand extends AbstractCommand
{
    protected $signature = 'redPacket:expire';

    protected $description = '返还过期未回答的红包金额';

    protected $expireTime = 24 * 60 * 60; //红包过期时间24小时

    protected $app;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var Dispatcher
     */
    protected $bus;

    protected $debugInfo = false;

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

        // 定时任务处理此条记录的时间，与用户最后参与领红包的时间增加 10 秒，以防时间临界点并发引起问题
        $compareTime = date("Y-m-d H:i:s", time() - $this->expireTime + 10);
        $query = RedPacket::query();
        $query->where('created_at', '<', $compareTime);
        $query->where('remain_money', '>', 0);
        $query->where('remain_number', '>', 0);
        $query->where('status', '=', RedPacket::RED_PACKET_STATUS_VALID); // 1:红包未过期
        $redPacket = $query->get();

        $bar = $this->createProgressBar(count($redPacket));
        $bar->start();
        $this->info('');

        $redPacket->map(function ($item) use ($bar) {
            // Start Transaction
            $this->connection->beginTransaction();
            try {
                $thread_id = $item->thread_id ? $item->thread_id : '';
                $this->info = '红包ID：'.$item->id
                .', 帖子ID:' . $thread_id
                    .',';
                if (empty($item->thread_id)) {
                    // 4:不作处理的异常红包
                    $this->changeRedPacketStatus($item->id, RedPacket::RED_PACKET_STATUS_UNTREATED);
                    $this->outDebugInfo('处理失败,帖子ID不存在');
                    $this->connection->commit();
                    return;
                }

                $order = Order::query()->where('thread_id', $item->thread_id)->first();
                if (empty($order)) {
                    $this->changeRedPacketStatus($item->id, RedPacket::RED_PACKET_STATUS_UNTREATED);
                    $this->outDebugInfo('处理失败,订单不存在');
                    $this->connection->commit();
                    return;
                }

                $thread = Thread::query()->where('id', $item->thread_id)->first();
                if (empty($thread)) {
                    $this->changeRedPacketStatus($item->id, RedPacket::RED_PACKET_STATUS_UNTREATED);
                    $this->outDebugInfo('处理失败,帖子id不存在于帖子表');
                    $this->connection->commit();
                    return;
                }
                if ($thread['type'] == Thread::TYPE_OF_TEXT) {
                    $return_change_type = UserWalletLog::TYPE_TEXT_RETURN_THAW;// 103 文字帖冻结返还
                    $return_change_desc = trans('wallet.return_text');//文字帖红包支出
                } else {
                    $return_change_type = UserWalletLog::TYPE_LONG_RETURN_THAW;// 113 长文帖冻结返还
                    $return_change_desc = trans('wallet.return_long');//长文帖红包支出
                }
                $data = [
                    'order_id' => $order['id'],
                    'thread_id' => $item->thread_id,
                    'post_id' => $item->post_id,
                    'change_type' => $return_change_type,
                    'change_desc' => $return_change_desc
                ];

                $userWallet = UserWallet::query()->where('user_id', $order['user_id'])->first();
                if (!empty($userWallet)) {
                    $userWallet = $userWallet->toArray();
                } else {
                    $this->changeRedPacketStatus($item->id, RedPacket::RED_PACKET_STATUS_UNTREATED);
                    $this->outDebugInfo('红包过期处理失败, 用户钱包异常');
                    $this->connection->commit();
                    return;
                }

                if ($item->payment_type == Order::PAYMENT_TYPE_WALLET) {
                    if ($userWallet['freeze_amount'] < $item->remain_money) {
                        // 改变订单状态 11:在异常订单处理中不进行处理的订单
                        $this->changeRedPacketStatus($item->id, RedPacket::RED_PACKET_STATUS_UNTREATED);
                        $this->outDebugInfo('红包过期处理失败, ' . '用户冻结金额:' . $userWallet['freeze_amount'] . ' < '.'退回金额:' . $item->remain_money);
                        $this->connection->commit();
                        return;
                    }
                }

                $debugInfo =    '返还用户id：' . $order['user_id']
                            . ', 退回金额：' . $item->remain_money
                            . ', 原可用金额：' . $userWallet['available_amount']
                            . ', 原冻结金额：' . $userWallet['freeze_amount']
                ;

                $user = User::query()->where('id', $thread['user_id'])->first();
                if (empty($user)) {
                    $this->changeRedPacketStatus($item->id, RedPacket::RED_PACKET_STATUS_UNTREATED);
                    $this->outDebugInfo('处理失败,用户不存在');
                    $this->connection->commit();
                    return;
                }

                if ($order['payment_type'] == Order::PAYMENT_TYPE_WALLET) {
                    $this->bus->dispatch(new ChangeUserWallet($user,
                                                              UserWallet::OPERATE_UNFREEZE,
                                                              $item->remain_money,
                                                              $data
                                         ));
                } elseif (
                    $order['payment_type']     == Order::PAYMENT_TYPE_WECHAT_NATIVE
                    || $order['payment_type']  == Order::PAYMENT_TYPE_WECHAT_WAP
                    || $order['payment_type']  == Order::PAYMENT_TYPE_WECHAT_JS
                    || $order['payment_type']  == Order::PAYMENT_TYPE_WECHAT_MINI
                ) {
                    // 其余支付类型 增加可用金额
                    $this->bus->dispatch(new ChangeUserWallet($user,
                                                              UserWallet::OPERATE_INCREASE,
                                                              $item->remain_money,
                                                              $data
                                         ));
                } else {
                    $this->changeRedPacketStatus($item->id, RedPacket::RED_PACKET_STATUS_UNTREATED);
                    $this->outDebugInfo('订单金额退还失败, 订单支付类型: ' . $item->payment_type . ', 不在处理范围内');
                    $this->connection->commit();
                    return;
                }

                $userWallet = UserWallet::query()->where('user_id', $order['user_id'])->first();
                if (!empty($userWallet)) {
                    $userWallet = $userWallet->toArray();
                } else {
                    $this->changeRedPacketStatus($item->id, RedPacket::RED_PACKET_STATUS_UNTREATED);
                    $this->outDebugInfo('红包过期处理失败, 用户钱包异常');
                    $this->connection->commit();
                    return;
                }

                $this->changeRedPacketStatus($item->id, RedPacket::RED_PACKET_STATUS_RETURN);
                $this->outDebugInfo($debugInfo
                                    . ', 现可用金额：' . $userWallet['available_amount']
                                    . ', 现冻结金额：' . $userWallet['freeze_amount']
                                    . ', 处理成功');
                $this->connection->commit();
            } catch (Exception $e) {
                $this->outDebugInfo($this->info .'处理失败' . $e->getMessage());
                $this->connection->rollback();
            }

            $bar->advance();
            $this->info('');
        });

        $bar->finish();

        $this->info('');
        $this->info('脚本执行 [完成]');
    }

    public function changeRedPacketStatus($id, $status){
        $redPacket = RedPacket::query()->lockForUpdate()->find($id);
        $redPacket->status = $status;
        $redPacket->save();
    }

    public function outDebugInfo($info){
        $this->info($this->info . $info);
        app('log')->info($this->info . $info);
    }
}
