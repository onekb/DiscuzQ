<?php

namespace App\Notifications\Messages\Database;

use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Support\Arr;

/**
 * 异常订单退款通知
 */
class AbnormalOrderRefundMessage extends SimpleMessage
{
    protected $actor;

    protected $data;

    public function __construct()
    {

    }

    public function setData(...$parameters)
    {
        // 解构赋值
        [$firstData, $actor, $data] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;

        $this->actor = $actor;
        $this->data = $data;

        $this->render();
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
            'title' => $this->getTitle(),
            'content' => '您的订单' . $this->data['order_sn'] . '存在异常，已向钱包返回订单金额' . $this->data['amount'] . '元。'
        ];

        return $build;
    }

}
