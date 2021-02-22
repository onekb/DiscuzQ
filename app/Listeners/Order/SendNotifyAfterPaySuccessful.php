<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *   http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Listeners\Order;

use App\Events\Order\Updated;
use App\Models\Order;
use App\Notifications\Messages\Wechat\RewardedScaleWechatMessage;
use App\Notifications\Messages\Wechat\RewardedWechatMessage;
use App\Notifications\Rewarded;

class SendNotifyAfterPaySuccessful
{
    /**
     * 当前订单
     *
     * @var Order
     */
    protected $order;

    protected $build;

    protected $actor;

    public function handle(Updated $event)
    {
        $this->order = $event->order;

        // 不发通知：问答的提问没有主题数据；购买用户组没人可收
        if (
            $this->order->type == Order::ORDER_TYPE_QUESTION
            || $this->order->type == Order::ORDER_TYPE_GROUP
            || $this->order->type == Order::ORDER_TYPE_TEXT
            || $this->order->type == Order::ORDER_TYPE_LONG
        ) {
            return;
        }

        // 判断是否是支付成功后
        if ($this->order->status != Order::ORDER_STATUS_PAID) {
            return;
        }

        switch ($this->order->type) {
            case Order::ORDER_TYPE_REGISTER: // 付费加入站点
                // 发送分成通知
                $this->sendScaleNotice('paid_site');
                break;
            case Order::ORDER_TYPE_REWARD: // 打赏
                // Tag 发送通知 (通知主题作者)
                $this->senPayeeUser();
                // 发送分成通知
                $this->sendScaleNotice('paid_reward');
                break;
            case Order::ORDER_TYPE_THREAD: // 付费主题
                // Tag 发送通知 (通知作者收款通知)
                $this->senPayeeUser();
                // 发送分成通知
                $this->sendScaleNotice('paid_thread');
                break;
            case Order::ORDER_TYPE_ONLOOKER: // 围观
                // Tag 发送通知 (发送给 问答人 收入分成通知 Tag 目前该用户上级不分成)
                $this->senPayeeUser();
                // Tag 发送通知 (发送给 答题人（第三方用户） 收入分成通知 Tag 目前该用户上级不分成)
                $this->order->thirdParty->notify(new Rewarded(RewardedWechatMessage::class, $this->order->user, $this->order));
                break;
            case Order::ORDER_TYPE_ATTACHMENT: // 附件付费
                // Tag 发送通知 (通知作者收款通知)
                $this->senPayeeUser();
                // 发送分成通知
                $this->sendScaleNotice('paid_attachment');
                break;
            default:
                break;
        }
    }

    /**
     * 通知收款人(payee)
     */
    public function senPayeeUser()
    {
        $this->order->payee->notify(new Rewarded(RewardedWechatMessage::class, $this->order->user, $this->order));
    }

    /**
     * 共用发送分成通知
     *
     * @param string $type payee 打赏/付费  user 注册
     */
    public function sendScaleNotice(string $type)
    {
        /**
         * 发送分成收入通知
         */
        if ($this->order->isScale()) {
            // 判断是发给 收款人/付款人 的上级
            switch ($type) {
                default:
                case 'paid_site':
                    // 付费站点加入
                    $userDistribution = $this->order->user->userDistribution;
                    break;
                case 'paid_reward': // 打赏
                case 'paid_thread': // 付费主题
                case 'paid_attachment': // 附件付费
                    $userDistribution = $this->order->payee->userDistribution;
                    break;
            }

            if (! empty($userDistribution)) {
                // 付款人 = 当前登录人
                $actor = $this->order->user;
                // Tag 发送通知
                $userDistribution->parentUser->notify(new Rewarded(RewardedScaleWechatMessage::class, $actor, $this->order, ['notify_type' => $type]));
            }
        }
    }
}
