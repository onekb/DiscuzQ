<?php

namespace App\Api\Controller\OrderV3;

use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use App\Models\Order;
use Discuz\Base\DzqController;

class ResourceOrderController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $user = $this->user;
        $order = Order::query()
            ->where([
                        'user_id' => $user->id,
                        'order_sn' => $this->inPut('orderSn'),
                    ])
            ->first();
        if(empty($order)){
            $this->outPut(ResponseCode::INVALID_PARAMETER);
        }
        $order = [
            'id' => $order->id,
            'orderSn' => (string) $order->order_sn,
            'amount' => $order->amount,
            'status' => $order->status,
            'type' => $order->type,
            'threadId' => $order->thread_id,
            'groupId' => $order->group_id,
            'updatedAt' => optional($order->updated_at)->format('Y-m-d H:i:s'),
            'createdAt' => optional($order->created_at)->format('Y-m-d H:i:s'),
        ];
        $this->outPut(ResponseCode::SUCCESS, '', $order);
    }
}
