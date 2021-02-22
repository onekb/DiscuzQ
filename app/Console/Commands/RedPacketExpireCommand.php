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
        // 定时任务处理此条记录的时间，与用户最后参与领红包的时间增加 10 秒，以防时间临界点并发引起问题
        $compareTime = date("Y-m-d H:i:s", time() - $this->expireTime + 10);
        $query = RedPacket::query();
        $query->where('created_at', '<', $compareTime);
        $query->where('remain_money', '>', 0);
        $query->where('remain_number', '>', 0);
        $redPacket = $query->get();

        $bar = $this->createProgressBar(count($redPacket));
        $bar->start();

        $redPacket->map(function ($item) use ($bar) {
            // Start Transaction
            $this->connection->beginTransaction();
            try {
                if (empty($item->thread_id)) {
                    $this->outPutDebugInfo('过期红包ID: ' . $item->id . ' -- 帖子ID不存在');
                    return;
                }

                $order = Order::query()->where('thread_id', $item->thread_id)->first();
                if (empty($order)) {
                    $this->outPutDebugInfo('删除过期红包ID: ' . $item->id
                                                . ' -- 帖子ID：' . $item->thread_id . ' -- 订单不存在');
                    return;
                }

                if ($order['payment_type'] != Order::PAYMENT_TYPE_WALLET) {
                    $this->outPutDebugInfo('过期红包ID: ' . $item->id
                                                . ' -- 帖子ID：' . $item->thread_id
                                                . ' -- 订单支付类型为：' . $order['payment_type']);
                    return;
                }

                $thread = Thread::query()->where('id', $item->thread_id)->first();
                if ($thread['type'] == Thread::TYPE_OF_TEXT) {
                    $return_change_type = UserWalletLog::TYPE_TEXT_RETURN_THAW;// 103 文字帖冻结返还
                    $return_change_desc = trans('wallet.return_text');//文字帖红包支出
                } else {
                    $return_change_type = UserWalletLog::TYPE_LONG_RETURN_THAW;// 113 长文帖冻结返还
                    $return_change_desc = trans('wallet.return_long');//长文帖红包支出
                }
                $data = [
                    'thread_id' => $item->thread_id,
                    'post_id' => $item->post_id,
                    'change_type' => $return_change_type,
                    'change_desc' => $return_change_desc
                ];

                $query = UserWallet::query();
                $query->where('user_id', $order['user_id']);
                $userWallet = $query->first();
                $debugInfo =
                    '红包ID：' . $item->id
                    . ' -- 帖子ID：' . $item->thread_id
                    . ' -- 返还用户id：' . $order['user_id']
                    . ' -- 退回金额：' . $item->remain_money
                    . ' -- 原可用金额：' . $userWallet['available_amount']
                    . ' -- 原冻结金额：' . $userWallet['freeze_amount']
                ;

                $this->bus->dispatch(new ChangeUserWallet($item->thread->user,
                                                          UserWallet::OPERATE_UNFREEZE,
                                                          $item->remain_money,
                                                          $data
                                     ));

                $query = UserWallet::query();
                $query->where('user_id', $order['user_id']);
                $userWallet = $query->first();
                $this->outPutDebugInfo(
                    $debugInfo
                            . ' -- 现可用金额：' . $userWallet['available_amount']
                            . ' -- 现冻结金额：' . $userWallet['freeze_amount']
                );

                $item->status = 0;
                $item->remain_money = 0.00;
                $item->remain_number = 0;
                $item->save();

                $this->outPutDebugInfo('红包ID: '.$item->id.' 更改保存成功');
                $this->connection->commit();
            } catch (Exception $e) {
                $this->connection->rollback();
                $this->outPutDebugInfo('红包ID:'. $item->id .' 过期处理异常');
                app('log')->info('红包过期处理异常: ' . $e->getMessage());
            }

            $bar->advance();
        });

        $bar->finish();
    }

    public function outPutDebugInfo($debugInfo){
        if ($this->debugInfo) {
            echo PHP_EOL . $debugInfo;

            $file_pointer = fopen("RedPacketExpireCommand.txt","a+");
            fwrite($file_pointer,(PHP_EOL .'['. date('Y-m-d h:i:s', time()). ']: ' . $debugInfo));
            fclose($file_pointer);
        }
    }
}
