<?php

namespace App\Notifications\Messages\Sms;

use App\Models\Order;
use App\Models\Thread;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * 内容支付通知 - 短信
 *
 * @package App\Notifications\Messages\Sms
 */
class RewardedSmsMessage extends SimpleMessage
{
    /**
     * @var Order $order
     */
    protected $order;

    protected $actor;

    protected $data;

    /**
     * @var UrlGenerator
     */
    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function setData(...$parameters)
    {
        [$firstData, $actor, $order, $data] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;

        $this->actor = $actor;
        $this->order = $order;
        $this->data = $data;

        $this->template();
    }

    public function template()
    {
        return $this->getSmsContent();
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {
        // 获取支付类型
        $orderTypeName = Order::enumType($this->order->type, function ($args) {
            return $args['value'];
        });

        // 获取作者实际金额
        $actualAmount = 0;
        switch ($this->order->type) {
            case Order::ORDER_TYPE_REWARD: // 2
                // 打赏
                $actualAmount = $this->order->calculateAuthorAmount(true);
                break;
            case Order::ORDER_TYPE_THREAD: // 3
                // 付费主题
                $actualAmount = $this->order->calculateAuthorAmount(true);
                break;
            case Order::ORDER_TYPE_QUESTION: // 5
                // 问答提问支付
                $actualAmount = $this->order->author_amount;
                break;
            case Order::ORDER_TYPE_ONLOOKER: // 6
                // 围观
                $actualAmount = $this->order->calculateOnlookersAmount(false);
                break;
            case Order::ORDER_TYPE_ATTACHMENT: // 7
                // 附件付费
                $actualAmount = $this->order->calculateAuthorAmount(true);
                break;
        }

        $threadTitle = $this->order->thread->getContentByType(Thread::CONTENT_LENGTH, true);

        /**
         * 设置父类 模板数据
         * @parem $user_id 支付人用户ID
         * @parem $user_name 支付人
         * @parem $order_sn 订单编号
         * @parem $payment_sn 支付编号
         * @parem $order_type_name 订单支付类型 (打赏/付费主题/付费用户组/问答回答收入/问答围观收入/付费附件)
         * @parem $actual_amount 实际获得金额
         * @parem $thread_id 主题ID
         * @parem $thread_title 主题标题/首帖内容 (如果有title是title，没有则是首帖内容)
         */
        $this->setTemplateData([
            '{$user_id}'         => $this->order->user->id,
            '{$user_name}'       => $this->order->user->username,
            '{$order_sn}'        => $this->order->order_sn,
            '{$payment_sn}'      => $this->order->payment_sn,
            '{$order_type_name}' => $orderTypeName,
            '{$actual_amount}'   => $actualAmount,
            '{$thread_id}'       => $this->order->thread->id,
            '{$thread_title}'    => $this->strWords($threadTitle),
        ]);

        // build data
        return $this->compiledArray();
    }

}
