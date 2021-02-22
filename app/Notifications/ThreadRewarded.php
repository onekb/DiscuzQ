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
use App\Models\UserWalletLog;
use App\Models\Order;
use App\Notifications\Messages\Database\ThreadRewardedMessage;
use App\Notifications\Messages\Wechat\ThreadRewardedWechatMessage;
use Discuz\Notifications\Messages\SimpleMessage;
use Discuz\Notifications\NotificationManager;
use Illuminate\Support\Collection;

/**
 * 得到悬赏通知
 *
 * @package App\Notifications
 */
class ThreadRewarded extends AbstractNotification
{

    protected $message;

    public $user;

    public $order;

    public $data;

    public $walletType;

    public $tplId = [
        'database' => 47,
        'wechat' => 48,
    ];

    /**
     * @var Collection
     */
    protected $messageRelationship;

    public function __construct($message, User $user, $order, $data, $walletType)
    {
        $this->message = app($message);
        $this->user = $user;
        $this->order = $order;
        $this->data = $data;
        $this->walletType = $walletType;

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
        return self::$tplData->where('id', $this->tplId[$type])->first();
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
        $message = $this->getMessage('database');
        $message->setData($this->getTplModel('database'), $this->user, $this->order, $this->data);

        return (new NotificationManager)->driver('database')->setNotification($message)->build();
    }

    public function toWechat($notifiable)
    {
        $message = $this->getMessage('wechat');
        $message->setData($this->getTplModel('wechat'), $this->user, $this->order, $this->data);

        return (new NotificationManager)->driver('wechat')->setNotification($message)->build();
    }

    protected function initNoticeMessage()
    {
        /**
         * init database message
         */
        $this->messageRelationship = collect();
        $this->messageRelationship['wechat'] = $this->message;

        // set public database message relationship
        $this->messageRelationship['database'] = app(ThreadRewardedMessage::class);

        $newData = (array)$this->data;
        if($this->walletType == UserWalletLog::TYPE_INCOME_THREAD_REWARD_RETURN){
            $this->tplId['database'] = 49;
            $this->data = array_merge($newData, ['notice_types_of' => 3]); // 过期通知
        }else{
            $this->tplId['database'] = 47;
            $this->data = array_merge($newData, ['notice_types_of' => 1]); // 收入通知
        }
        app('log')->info(__LINE__ . '行：给被采纳者用户准备通知信息的模板。'.$this->messageRelationship['wechat']->tplId);
        $this->tplId['wechat'] = $this->messageRelationship['wechat']->tplId;

    }
}
