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

namespace App\Notifications;

use App\Models\User;
use App\Models\UserWalletCash;
use App\Notifications\Messages\Database\WithdrawalMessage;
use App\Notifications\Messages\MiniProgram\WithdrawalMiniProgramMessage;
use App\Notifications\Messages\Sms\WithdrawalSmsMessage;
use App\Notifications\Messages\Wechat\WithdrawalWechatMessage;
use Discuz\Notifications\NotificationManager;

/**
 * 提现通知
 *
 * @package App\Notifications
 */
class Withdrawal extends AbstractNotification
{
    public $actor;

    public $cash;

    public $data;

    /**
     * @var array
     */
    public $tplId;

    public function __construct(User $actor, UserWalletCash $cash, $data = [])
    {
        $this->actor = $actor;
        $this->cash = $cash;
        $this->data = $data;

        /**
         * 初始化要发送的模板中，对应的 tplId
         */
        $this->initNoticeMessage();

        $this->setTemplate();
    }

    /**
     * 设置所有开启中的，要发送的模板
     * 查询到数据集合后，存放静态区域
     */
    protected function setTemplate()
    {
        self::getTemplate($this->tplId);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // 获取已开启的通知频道
        return $this->getNotificationChannels();
    }

    public function getTplModel($type)
    {
        return self::$tplData->where('notice_id', $this->tplId[$type])->first();
    }

    public function toDatabase($notifiable)
    {
        $message = app(WithdrawalMessage::class);
        $message->setData($this->getTplModel('database'), $this->actor, $this->cash);

        return (new NotificationManager)->driver('database')->setNotification($message)->build();
    }

    public function toWechat($notifiable)
    {
        $message = app(WithdrawalWechatMessage::class);
        $message->setData($this->getTplModel('wechat'), $this->actor, $this->cash, $this->data);

        return (new NotificationManager)->driver('wechat')->setNotification($message)->build();
    }

    public function toSms($notifiable)
    {
        $message = app(WithdrawalSmsMessage::class);
        $message->setData($this->getTplModel('sms'), $this->actor, $this->cash, $this->data);

        return (new NotificationManager)->driver('sms')->setNotification($message)->build();
    }

    public function toMiniProgram($notifiable)
    {
        $message = app(WithdrawalMiniProgramMessage::class);
        $message->setData($this->getTplModel('miniProgram'), $this->actor, $this->cash, $this->data);

        return (new NotificationManager)->driver('miniProgram')->setNotification($message)->build();
    }

    /**
     * 初始化对应通知类型
     */
    protected function initNoticeMessage()
    {
        // set tpl id 获取提现状态
        $this->cashTpl($this->cash->cash_status);
    }

    /**
     * @param int $status 提现状态：1：待审核，2：审核通过，3：审核不通过，4：待打款，5：已打款，6：打款失败
     */
    public function cashTpl(int $status)
    {
        // 非失败状态
        if (UserWalletCash::notificationByWhich($status)) {
            // 提现通知
            $this->tplId = [
                'database'    => 'system.withdraw.noticed',
                'wechat'      => 'wechat.withdraw.noticed',
                'sms'         => 'sms.withdraw.noticed',
                'miniProgram' => 'miniprogram.withdraw.noticed',
            ];
        } else {
            // 提现失败通知
            $this->tplId = [
                'database'    => 'system.withdraw.withdraw',
                'wechat'      => 'wechat.withdraw.withdraw',
                'sms'         => 'sms.withdraw.withdraw',
                'miniProgram' => 'miniprogram.withdraw.withdraw',
            ];
        }
    }
}
