<?php

namespace App\Notifications\Messages\Database;

use App\Models\Order;
use App\Models\Question;
use App\Models\Thread;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Support\Arr;

class ReceiveRedPacketMessage extends SimpleMessage
{
    protected $model;

    protected $actor;

    protected $data;

    /**
     * @var string 通知类型
     */
    public $noticeType;

    /**
     * @var array 通知模型数据
     */
    public $initData;

    /**
     * @var Order
     */
    public $order = null;

    /**
     * @var Question
     */
    public $question = null;

    public function __construct()
    {
        //
    }

    public function setData(...$parameters)
    {
        // 解构赋值
        [$firstData, $actor, $model, $data] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;

        $this->actor = $actor;
        $this->model = $model;
        $this->data = $data;

        $this->setModel($model);

        // $this->setChannelName($messageClass);

        $this->initData();
    }

    public function setModel($model)
    {
        if ($model instanceof Order) {
            $this->order = $model;
        } elseif ($model instanceof Question) {
            $this->question = $model;
        }
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {
        return $data;
    }

    public function initData()
    {
        $this->initData = [
            'user_id' => 0,
            'order_id' => 0,    // 订单 id
            'thread_id' => 0,   // 必传 可为0 主题关联 id
            'thread_username' => 0,
            'thread_title' => 0,
            'content' => '',
            'thread_created_at' => '',
            'amount' => 0, // 获取上级的实际分成金额数
            'order_type' => 0,  // 1注册 2打赏 3付费主题 4付费用户组
            'notice_type' => Arr::get($this->data, 'notice_types_of'), // 1收入通知 2分成通知 3过期通知
        ];
    }

    public function render()
    {
        if (! is_null($this->order)) {
            $this->noticeByOrder();
        } elseif (! is_null($this->question)) {
            $this->noticeByQuestion();
        }

        return $this->initData;
    }

    /**
     * 当存在订单时，发送 收入/分成 通知
     */
    public function noticeByOrder()
    {
        $this->initData['user_id'] = $this->actor->id; // 付款人ID
        $this->initData['order_id'] = $this->order->id;
        $this->initData['order_type'] = $this->order->type; // 1：注册，2：打赏，3：付费主题，4：付费用户组
        $this->initData['thread_id'] = $this->order->thread->id; // 必传
        $this->initData['thread_username'] = $this->order->thread->user->username; // 必传主题用户名
        $this->initData['thread_title'] = $this->order->thread->title;
        $this->initData['thread_created_at'] = $this->order->thread->formatDate('created_at');
        $this->initData['amount'] = $this->data['raw']['actual_amount']; // 支付金额 - 分成金额 (string精度问题)
        $this->build();


        // 当有订单时 必传 是否是分成金额
        $this->initData['isScale'] = $this->order->isScale();
    }

    /**
     * 赋值内容
     */
    public function build()
    {
        $content = '';

        if (! is_null($this->order)) {
            $content = $this->order->thread->getContentByType(Thread::CONTENT_LENGTH);
        }

        $this->initData['content'] = $content;
    }
}
