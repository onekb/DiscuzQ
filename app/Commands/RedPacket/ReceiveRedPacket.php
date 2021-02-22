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

namespace App\Commands\RedPacket;

use App\Commands\Wallet\ChangeUserWallet;
use App\Common\Coupon;
use App\Models\Order;
use App\Models\RedPacket;
use App\Models\Thread;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Foundation\EventsDispatchTrait;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Database\ConnectionInterface;

class ReceiveRedPacket
{
    use AssertPermissionTrait;
    use EventsDispatchTrait;

    protected $connection;

    private $thread;

    private $post;

    private $redPacket;

    private $expendUser;

    private $incomeUser;

    private $bus;

    /**
     * @param $thread
     * @param $post
     * @param $redPacket
     * @param $expendUser
     * @param $incomeUser
     * @throws Exception
     */
    public function __construct($thread,$post,$redPacket,$expendUser,$incomeUser)
    {

        $this->thread = $thread;
        $this->post = $post;
        $this->redPacket = $redPacket;
        $this->expendUser = $expendUser;
        $this->incomeUser = $incomeUser;

    }

    /**
     * @param ConnectionInterface $connection
     * @param BusDispatcher $bus
     * @return void
     * @throws Exception
     */
    public function handle(ConnectionInterface $connection, BusDispatcher $bus)
    {
        $this->connection = $connection;
        $this->bus = $bus;

        $order = Order::query()->where('thread_id', $this->thread['id'])->first();
        if (empty($order)) {
            throw new Exception(trans('redpacket.thread_order_illegal'));
        }

        if ($this->redPacket['rule'] == 1) { // 发放规则，0定额，1随机
            $generateRedPacket = new Coupon($this->redPacket['remain_money'],$this->redPacket['remain_number']);
            $redPacketItems = $generateRedPacket->handle();
            $prepareChangeAmount = $redPacketItems['items'][0];
        } else {
            $prepareChangeAmount = $this->redPacket['remain_money'] / $this->redPacket['remain_number'];
        }

        if ($this->thread['type'] == Thread::TYPE_OF_TEXT) {
            $expend_change_type = UserWalletLog::TYPE_EXPEND_TEXT;// 100 文字帖红包支出
            $income_change_type = UserWalletLog::TYPE_INCOME_TEXT;// 102 文字帖红包收入
            $expend_change_desc = trans('wallet.expend_text');//文字帖红包支出
            $income_change_desc = trans('wallet.income_text');//文字帖红包收入
        } else {
            $expend_change_type = UserWalletLog::TYPE_EXPEND_LONG;// 110 长字帖红包支出
            $income_change_type = UserWalletLog::TYPE_INCOME_LONG;// 112 长字帖红包收入
            $expend_change_desc = trans('wallet.expend_long');//长文帖红包支出
            $income_change_desc = trans('wallet.income_long');//长文帖红包收入
        }

        // Start Transaction
        $this->connection->beginTransaction();
        try {
            $redPacketData = RedPacket::query()->lockForUpdate()->find($this->redPacket['id']);
            $redPacketData->remain_money = $this->redPacket['remain_money'] - $prepareChangeAmount;
            $redPacketData->remain_number = $this->redPacket['remain_number'] - 1;
            if ($redPacketData->remain_money == 0 && $redPacketData->remain_number == 0) {
                $redPacketData->status = RedPacket::RED_PACKET_STATUS_BROUGHT_OUT; // 2:红包已领完
            }
            $redPacketData->save();

            if($order->payment_type == Order::PAYMENT_TYPE_WALLET){
                //减少发帖人冻结金额
                $data = [
                    'order_id' => $order->id,
                    'thread_id' => $this->thread['id'],
                    'post_id' => $this->post['id'],
                    'change_type' => $expend_change_type,
                    'change_desc' => $expend_change_desc
                ];
                $this->bus->dispatch(new ChangeUserWallet($this->expendUser, UserWallet::OPERATE_DECREASE_FREEZE, $prepareChangeAmount, $data));
            }

            //增加领取人可用金额
            $data = [
                'order_id' => $order->id,
                'thread_id' => $this->thread['id'],
                'post_id' => $this->post['id'],
                'change_type' => $income_change_type,
                'change_desc' => $income_change_desc
            ];
            $this->bus->dispatch(new ChangeUserWallet($this->incomeUser, UserWallet::OPERATE_INCREASE, $prepareChangeAmount, $data));

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollback();
            app('log')->info('红包ID:'.$this->redPacket['id'].'领取异常:' . $e->getMessage());

            throw $e;
        }

    }
}
