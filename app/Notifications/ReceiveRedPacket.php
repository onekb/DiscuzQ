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
use App\Notifications\Messages\Database\ReceiveRedPacketMessage;
use App\Notifications\Messages\MiniProgram\ReceiveRedPacketMiniProgramMessage;
use App\Notifications\Messages\Sms\ReceiveRedSmsMessage;
use App\Notifications\Messages\Wechat\ReceiveRedPacketWechatMessage;
use Discuz\Notifications\Messages\SimpleMessage;
use Discuz\Notifications\NotificationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * 得到红包通知
 *
 * @package App\Notifications
 */
class ReceiveRedPacket extends AbstractNotification
{
    public $user;

    public $model;

    public $data;

    protected $message;

    public $tplId = [
        'database'    => 'system.red_packet.gotten',
        'wechat'      => 'wechat.red_packet.gotten',
        'sms'         => 'sms.red_packet.gotten',
        'miniProgram' => 'miniprogram.red_packet.gotten'
    ];

    /**
     * @var Collection
     */
    protected $messageRelationship;

    public function __construct(User $user, Model $model, $data = [])
    {
        $this->setTemplate();
        $this->user = $user;
        $this->model = $model;
        $this->data = $data;
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

    /**
     * @param string $type
     * @return SimpleMessage
     */
    public function getMessage(string $type)
    {
        return $this->messageRelationship->get($type);
    }

    public function toDatabase($notifiable)
    {
        $message = app(ReceiveRedPacketMessage::class);
        $message->setData($this->getTplModel('database'), $this->user, $this->model, $this->data);

        return (new NotificationManager)->driver('database')->setNotification($message)->build();
    }

    public function toWechat($notifiable)
    {
        $message = app(ReceiveRedPacketWechatMessage::class);
        $message->setData($this->getTplModel('wechat'), $this->user, $this->model, $this->data);
        return (new NotificationManager)->driver('wechat')->setNotification($message)->build();
    }

    public function toSms($notifiable)
    {
        $message = app(ReceiveRedSmsMessage::class);
        $message->setData($this->getTplModel('sms'), $this->user, $this->model, $this->data);
        return (new NotificationManager)->driver('sms')->setNotification($message)->build();
    }

    public function toMiniProgram($notifiable)
    {
        $message = app(ReceiveRedPacketMiniProgramMessage::class);
        $message->setData($this->getTplModel('miniProgram'), $this->user, $this->model, $this->data);

        return (new NotificationManager)->driver('miniProgram')->setNotification($message)->build();
    }

}
