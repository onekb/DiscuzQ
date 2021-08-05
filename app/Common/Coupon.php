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

namespace App\Common;

use App\Exceptions\TradeErrorException;

/**
 * 红包分配算法
 *
 *      example
 *      $coupon = new Coupon(200, 5);
 *      $res = $coupon->handle();
 *      print_r($res);
 * @author Flc <2018-04-06 20:09:53>
 * @see http://flc.ren | http://flc.io | https://github.com/flc1125
 */
class Coupon
{
    /**
     * 红包金额
     * @var float
     */
    protected $amount;

    /**
     * 红包个数
     * @var int
     */
    protected $num;

    /**
     * 领取的红包最小金额
     * @var float
     */
    protected $coupon_min;

    /**
     * 红包分配结果
     * @var array
     */
    protected $items = [];

    /**
     * 初始化
     * @param float $amount     红包金额（单位：元）最多保留2位小数
     * @param int   $num        红包个数
     * @param float $coupon_min 每个至少领取的红包金额
     */
    public function __construct($amount, $num = 1, $coupon_min = 0.01)
    {
        $this->amount = $amount;
        $this->num = $num;
        $this->coupon_min = $coupon_min;
    }

    /**
     * 处理返回
     * @return array
     */
    public function handle()
    {
        $this->amount = floatval(sprintf('%.2f', $this->amount));
        $validAmount = floatval(sprintf('%.2f', $this->coupon_min * $this->num));

        // A. 验证
        if ($this->amount < $validAmount) {
            throw new TradeErrorException('红包总金额必须≥' . $validAmount . '元', 500);
        }

        // B. 分配红包
        $this->apportion();

        return [
            'items' => $this->items,
        ];
    }

    /**
     * 分配红包
     */
    protected function apportion()
    {
        $num = $this->num;  // 剩余可分配的红包个数
        $amount = $this->amount;  //剩余可领取的红包金额

        while ($num >= 1) {
            // 剩余一个的时候，直接取剩余红包
            if ($num == 1) {
                $coupon_amount = $this->decimal_number($amount);
            } else {
                $avg_amount = $this->decimal_number($amount / $num);  // 剩余的红包的平均金额

                $coupon_amount = $this->decimal_number(
                    $this->calcCouponAmount($avg_amount, $amount, $num)
                );
            }
            $this->items[] = $coupon_amount; // 追加分配

            $amount -= $coupon_amount;
            --$num;
        }

        shuffle($this->items);  //随机打乱
    }

    /**
     * 计算分配的红包金额
     *
     * @param float $avg_amount 每次计算的平均金额
     * @param float $amount     剩余可领取金额
     * @param int   $num        剩余可领取的红包个数
     * @return float
     */
    protected function calcCouponAmount($avg_amount, $amount, $num)
    {
        // 如果平均金额小于等于最低金额，则直接返回最低金额
        if ($avg_amount <= $this->coupon_min) {
            return $this->coupon_min;
        }

        // 浮动计算
        $coupon_amount = $this->decimal_number($avg_amount * (1 + $this->apportionRandRatio()));

        // 如果低于最低金额或超过可领取的最大金额，则重新获取
        if ($coupon_amount < $this->coupon_min
            || $coupon_amount > $this->calcCouponAmountMax($amount, $num)
        ) {
            return $this->calcCouponAmount($avg_amount, $amount, $num);
        }

        return $coupon_amount;
    }

    /**
     * 计算分配的红包金额-可领取的最大金额
     * @param float $amount
     * @param int   $num
     */
    protected function calcCouponAmountMax($amount, $num)
    {
        return $this->coupon_min + $amount - $num * $this->coupon_min;
    }

    /**
     * 红包金额浮动比例
     */
    protected function apportionRandRatio()
    {
        // 60%机率获取剩余平均值的大幅度红包（可能正数、可能负数）
        if (rand(1, 100) <= 60) {
            return rand(-70, 70) / 100; // 上下幅度70%
        }

        return rand(-30, 30) / 100; // 其他情况，上下浮动30%；
    }

    /**
     * 格式化金额，保留2位
     * @param float $amount
     * @return float
     */
    protected function decimal_number($amount)
    {
        return sprintf('%01.2f', round($amount, 2));
    }

}
