<?php

namespace App\Notifications\Messages\Database;

use App\Models\Order;
use App\Models\Thread;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Support\Arr;

class ThreadRewardedExpiredMessage extends SimpleMessage
{
    protected $user;

    protected $order;

    protected $data;

    public function __construct()
    {
        //
    }

    public function setData(...$parameters)
    {
        // 解构赋值
        [$firstData, $user, $order,$data] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;
        $this->user = $user;
        $this->order = $order;
        $this->data = $data;
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {
        return $data;
    }

    public function render()
    {
        $build = [
            'user_id' => $this->user->id,
            'order_id' => $this->order->id,    // 订单 id
            'thread_id' => $this->data['raw']['thread_id'],   // 必传 可为0 主题关联 id
            'thread_username' => $this->data['raw']['actor_username'],
            'thread_title' => $this->data['raw']['content'],
            'content' => $this->data['raw']['content'],
            'thread_created_at' => $this->data['raw']['created_at'],
            'amount' => $this->data['raw']['actual_amount'], // 获取上级的实际分成金额数
            'order_type' => $this->order->type,  // 1注册 2打赏 3付费主题 4付费用户组
            'notice_type' => Arr::get($this->data, 'notice_types_of'), // 1收入通知 2分成通知 3过期通知
        ];

        return $build;
    }
}
