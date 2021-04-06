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

namespace App\Listeners\RedPacket;

use App\Events\Post\Saved;
use App\Models\Order;
use App\Models\RedPacket;
use App\Models\Thread;
use App\Models\UserWalletLog;
use App\Validators\RedPacketValidator;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Foundation\EventsDispatchTrait;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class SaveRedPacketToDatabase
{
    use EventsDispatchTrait;

    /**
     * @var RedPacketValidator
     */
    protected $redPacketValidator;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var SettingsRepository
     */
    protected $settings;

    /**
     * @var BusDispatcher
     */
    protected $bus;

    public $baseInfo = '';

    public $debugInfo = false; // false:默认不输出调试信息到日志上

    public function __construct(
        EventDispatcher $eventDispatcher,
        RedPacketValidator $redPacketValidator,
        ConnectionInterface $connection,
        SettingsRepository $settings,
        BusDispatcher $bus
    ) {
        $this->events = $eventDispatcher;
        $this->redPacketValidator = $redPacketValidator;
        $this->connection = $connection;
        $this->settings = $settings;
        $this->bus = $bus;
    }

    /**
     * @param Saved $event
     * @throws ValidationException
     * @throws Exception
     */
    public function handle(Saved $event)
    {
        $post = $event->post;
        $actor = $event->actor;
        $data = $event->data;
        $this->baseInfo =   '访问用户id:'  . $actor->id . '(' . $actor->username . ')'.
                            ',访问帖子id:' . $post->thread->id.
                            ',post_id:'   . $post->id.
                            ',msg:';

        if (($post->thread->type != Thread::TYPE_OF_TEXT || $post->thread->type != Thread::TYPE_OF_LONG)
             && (empty($data['attributes']['redPacket']['money'])
                || empty($data['attributes']['redPacket']['number']))) {
            $this->outDebugInfo('保存红包到数据库：不是有效红包帖或红包已分完');
            return;
        }

        // 是否为草稿
        $isDraft = Arr::get($data, 'attributes.is_draft');

        // 判断是否是创建
        if (empty($isDraft)) {
            $id = $data['attributes']['id'];
            if (empty($id)) {
                if (!($post->is_first == 1 && ($post->wasRecentlyCreated == true))) {
                    $this->outDebugInfo('保存红包到数据库：不是首帖创建内容');
                    return;
                }
            }
        }

        $thread = Thread::query()->where('id',$post->thread_id)->first();
        if (empty($thread['is_red_packet'])) {
            $this->outDebugInfo('保存红包到数据库：该帖不为红包帖');
            return;
        }

        $threadData = Arr::get($data, 'attributes');
        if (empty($threadData)) {
            throw new Exception(trans('post.post_thread_missing_parameter')); // 帖子缺失参数
        }

        /**
         * Validator
         *
         * @see redPacketValidator
         */
        $threadData['actor'] = $actor;
        //草稿不验证
        if (!$isDraft) {
            $this->redPacketValidator->valid($threadData['redPacket']);
        }

        $rule = $threadData['redPacket']['rule'];
        $condition = $threadData['redPacket']['condition'];

        $likenum = Arr::get($threadData, 'redPacket.likenum', 0);
        if ($likenum > 250) {
            throw new Exception(trans('redpacket.likenum_lg_limit'));
        }

        $number = $threadData['redPacket']['number'];

        //草稿不验证
        if (!$isDraft) {
            $relationshipsData = Arr::get($data, 'relationships');
            if (empty($relationshipsData['redpacket']['data']['order_id'])) {
                throw new Exception(trans('redpacket.redpacket_order_not_found'));
            }
            $order_id = $relationshipsData['redpacket']['data']['order_id'];
            $order = Order::query() ->where(['order_sn' => $order_id, 'status' => Order::ORDER_STATUS_PAID])
                ->first();
            if (empty($order)) {
                throw new Exception(trans('order.order_not_found'));
            }
            $money = $order['amount'];
        } else {
            $money = $threadData['redPacket']['money'];
        }

        //红包领取规则 0:定额 1:随机
        if ($rule == 1 && $number > 200) { // 随机红包金额最大值为 200
            throw new Exception(trans('redpacket.money_lg_limit'));
        }

        $singleMoney = $money / $number;
        if ($rule == 0 && $singleMoney > 200) { // 定额红包单个红包金额最大值为 200
            throw new Exception(trans('redpacket.money_lg_limit'));
        }
        if ($singleMoney < 0.01) {
            if ($rule == 1) {
                throw new Exception(trans('redpacket.redpacket_money_illegal'));
            } else {
                throw new Exception(trans('redpacket.redpacket_average_money_illegal'));
            }
        }

        $remain_money = $money;
        $remain_number = $number;
        $status = 1;

        // Start Transaction
        $this->connection->beginTransaction();
        try {
            /**
             * Create RedPacket
             *
             * @var RedPacket $redPacket
             */
            $redPacket = RedPacket::query() ->where('thread_id', $post->thread_id)
                                            ->where('post_id',$post->id)
                                            ->first();

            $redPacket = RedPacket::creation(
                $thread['id'],
                $post->id,
                $rule,
                $condition,
                $likenum,
                $money,
                $number,
                $remain_money,
                $remain_number,
                $status,
                $redPacket
            );
            $redPacket->save();

            $threadData = Arr::get($data, 'relationships');

            $orderSn = !empty($threadData['redpacket']) ? $threadData['redpacket']['data']['order_id'] : '';
            if (! empty($orderSn)) {
                /**
                 * Update Order relation thread_id
                 *
                 * @var Order $order
                 */
                $order = Order::query()->where('order_sn', $orderSn)->firstOrFail();
                if (empty($order)) {
                    throw new Exception(trans('redpacket.redpacket_order_thread_id_not_null'));
                }
                $order->thread_id = $post->thread_id;
                $order->save();

                /**
                 * Update WalletLog relation question_id
                 *
                 * @var Order $order
                 * @var UserWalletLog $walletLog
                 */
                if ($order->payment_type == Order::PAYMENT_TYPE_WALLET) {
                    if ($thread['type'] == Thread::TYPE_OF_TEXT) {
                        $change_type = UserWalletLog::TYPE_TEXT_FREEZE;
                    } elseif ($thread['type'] == Thread::TYPE_OF_LONG) {
                        $change_type = UserWalletLog::TYPE_LONG_FREEZE;
                    } else {
                        $change_type = UserWalletLog::TYPE_TEXT_FREEZE;
                    }

                    $walletLog = UserWalletLog::query()->where([
                        'user_id' => $actor->id,
                        'order_id' => $order->id,
                        'change_type' => $change_type,
                    ])->first();
                    if (empty($walletLog)) {
                        throw new Exception(trans('redpacket.user_wallet_log_null'));
                    }
                    $walletLog->thread_id = $redPacket->thread_id;
                    $walletLog->post_id = $redPacket->post_id;
                    $walletLog->save();

                }
            }

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollback();
            app('log')->info($this->baseInfo . '保存红包到数据库异常:' . $e->getMessage());

            throw $e;
        }

        // 延迟执行事件
        $this->dispatchEventsFor($redPacket, $actor);
    }

    public function outDebugInfo($info)
    {
        if ($this->debugInfo) {
            app('log')->info($this->baseInfo . $info);
        }
    }
}
