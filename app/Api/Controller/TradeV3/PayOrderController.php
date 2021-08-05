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

namespace App\Api\Controller\TradeV3;

use App\Commands\Trade\PayOrder;
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\Order;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class PayOrderController extends DzqController
{
    /**
     * @var Dispatcher
     */
    protected $bus;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$userRepo->canPayOrder($this->user)) {
            throw new PermissionDeniedException('您没有权限支付订单');
        }
        return true;
    }

    public function prefixClearCache($user)
    {
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_USER_PAY_ORDERS . $user->id);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_USER_REWARD_ORDERS . $user->id);
    }

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    public function main()
    {
        try {
            // 兼容原来封装的逻辑
            $orderSn = $this->inPut("orderSn");
            $paymentType = $this->inPut("paymentType");
            $payPassword = $this->inPut("payPassword");
            if (empty($orderSn) || empty($paymentType) ||
                ($paymentType == Order::PAYMENT_TYPE_WALLET && empty($payPassword))) {
                $this->outPut(ResponseCode::INVALID_PARAMETER);
            }

            $data = array(
                'order_sn' => $orderSn,
                'payment_type' => $paymentType
            );
            if (!empty($payPassword)) {
                $data['pay_password'] = $payPassword;
            }
            $data = collect($data);
            $payOrder = $this->bus->dispatch(
                new PayOrder($orderSn, $this->user, $data)
            );
        } catch (\Exception $e) {
            $this->info('订单支付失败,订单id:' . $orderSn, [$e->getTraceAsString()]);
            $this->outPut(ResponseCode::INTERNAL_ERROR, $e->getMessage());
        }

        $result = [];
        $payOrderResult = $payOrder->payment_params;
        if ($paymentType == Order::PAYMENT_TYPE_WALLET) {
            $payOrderResult = $payOrderResult['wallet_pay'] ?? [];
            $result = [
                'id' => $payOrder->id,
                'desc' => $payOrder->body,
                'walletPayResult' => $this->camelData($payOrderResult),
                'wechatPayResult' => []
            ];
        } else {
            $payOrderResult['wechatQrcode'] = $payOrderResult['wechat_qrcode'] ?? '';
            $result = [
                'id' => $payOrder->id,
                'desc' => $payOrder->body,
                'walletPayResult' => [],
                'wechatPayResult' => $this->camelData($payOrderResult)
            ];
        }

        if (isset($payOrderResult['result']) && $payOrderResult['result'] == 'fail') {
            $this->outPut(ResponseCode::PAY_ORDER_FAIL, $payOrderResult['message'], $result);
        }

        $this->outPut(ResponseCode::SUCCESS, '', $result);
    }
}
