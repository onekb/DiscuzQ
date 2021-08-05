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

namespace App\Commands\Trade\Notify;

use App\Events\Order\Updated;
use App\Models\Order;
use App\Models\OrderChildren;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use App\Settings\SettingsRepository;
use ErrorException;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionInterface;

class WalletPayNotify
{
    use NotifyTrait;

    /**
     * @var array
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @param ConnectionInterface $connection
     * @param Dispatcher $events
     * @param SettingsRepository $setting
     * @return array
     * @throws ErrorException
     */
    public function handle(ConnectionInterface $connection, Dispatcher $events, SettingsRepository $setting)
    {
        $log = app('payLog');
        try {
            $log->info('notify', $this->data);
        } catch (Exception $e) {
            goto todo;
        }

        todo:
        $payment_sn = $this->data['payment_sn'];//商户交易号
        $trade_no = $this->data['payment_sn'];//微信交易号

        //开始事务
        $connection->beginTransaction();
        try {
            // 从钱包余额中扣除订单金额
            $userWallet = UserWallet::query()->lockForUpdate()->find($this->data['user_id']);
            $changeAvailableAmount = bcsub($userWallet->available_amount, $this->data['amount'], 2);
            $changeFreezeAmount = bcadd($userWallet->freeze_amount, $this->data['amount'], 2);
            $updateData = ['available_amount' => $changeAvailableAmount];

            // 记录钱包变更明细
            switch ($this->data['type']) {
                case Order::ORDER_TYPE_REGISTER:
                    $change_type = UserWalletLog::TYPE_EXPEND_REGISTER;
                    $change_type_lang = 'wallet.expend_register';
                    break;
                case Order::ORDER_TYPE_REWARD:
                    $change_type = UserWalletLog::TYPE_EXPEND_REWARD;
                    $change_type_lang = 'wallet.expend_reward';
                    break;
                case Order::ORDER_TYPE_THREAD:
                    $change_type = UserWalletLog::TYPE_EXPEND_THREAD;
                    $change_type_lang = 'wallet.expend_thread';
                    break;
                case Order::ORDER_TYPE_GROUP:
                    $change_type = UserWalletLog::TYPE_EXPEND_GROUP;
                    $change_type_lang = 'wallet.expend_group';
                    break;
                case Order::ORDER_TYPE_QUESTION:
                    $change_type = UserWalletLog::TYPE_QUESTION_FREEZE;
                    $change_type_lang = 'wallet.question_freeze_desc';
                    // 钱包&钱包日志 增加冻结金额数
                    $updateData = ['available_amount' => $changeAvailableAmount, 'freeze_amount' => $changeFreezeAmount];
                    $freezeAmount = $this->data['amount'];
                    break;
                case Order::ORDER_TYPE_ONLOOKER:
                    $change_type = UserWalletLog::TYPE_EXPEND_ONLOOKER;
                    $change_type_lang = 'wallet.expend_onlooker';
                    break;
                case Order::ORDER_TYPE_ATTACHMENT:
                    $change_type = UserWalletLog::TYPE_EXPEND_ATTACHMENT;
                    $change_type_lang = 'wallet.expend_attachment';
                    break;
                case Order::ORDER_TYPE_RENEW:
                    $change_type = UserWalletLog::TYPE_EXPEND_RENEW;
                    $change_type_lang = 'wallet.expend_renew';
                    break;
                case Order::ORDER_TYPE_TEXT:
                    $change_type = UserWalletLog::TYPE_TEXT_FREEZE;
                    $change_type_lang = 'wallet.freeze_text';
                    // 钱包&钱包日志 增加文字帖红包冻结金额数
                    $updateData = ['available_amount' => $changeAvailableAmount, 'freeze_amount' => $changeFreezeAmount];
                    $freezeAmount = $this->data['amount'];
                    break;
                case Order::ORDER_TYPE_LONG:
                    $change_type = UserWalletLog::TYPE_LONG_FREEZE;
                    $change_type_lang = 'wallet.freeze_long';
                    // 钱包&钱包日志 增加长文帖红包冻结金额数
                    $updateData = ['available_amount' => $changeAvailableAmount, 'freeze_amount' => $changeFreezeAmount];
                    $freezeAmount = $this->data['amount'];
                    break;
                case Order::ORDER_TYPE_REDPACKET:
                    $change_type = UserWalletLog::TYPE_REDPACKET_FREEZE;
                    $change_type_lang = 'wallet.redpacket_freeze';
                    $updateData = ['available_amount' => $changeAvailableAmount, 'freeze_amount' => $changeFreezeAmount];
                    $freezeAmount = $this->data['amount'];
                    break;
                case Order::ORDER_TYPE_QUESTION_REWARD:
                    $change_type = UserWalletLog::TYPE_QUESTION_REWARD_FREEZE;
                    $change_type_lang = 'wallet.question_reward_freeze';
                    $updateData = ['available_amount' => $changeAvailableAmount, 'freeze_amount' => $changeFreezeAmount];
                    $freezeAmount = $this->data['amount'];
                    break;
                case Order::ORDER_TYPE_MERGE:
                    //合并订单支付 包含  红包 + 悬赏，这里需要拆分成两条钱包流水记录，
                    //获取 order_children 子订单中红包、悬赏金额
                    $order_children = OrderChildren::query()->where('order_sn', $this->data['order_sn'])->get(['type', 'amount'])->toArray();
                    $redpacket_log = $reward_log = [];
                    foreach ($order_children as $val){
                        switch ($val['type']){
                            case OrderChildren::TYPE_REDPACKET:
                                $redpacket_log = $val;
                                break;
                            case OrderChildren::TYPE_REWARD:
                                $reward_log = $val;
                                break;
                            default:
                                break;
                        }
                    }
                    //红包记录
                    if(!empty($redpacket_log)){
                        $change_type = UserWalletLog::TYPE_REDPACKET_FREEZE;
                        $change_type_lang = 'wallet.redpacket_freeze';
                        $updateData = [
                            'available_amount' => bcsub($userWallet->available_amount, $redpacket_log['amount'], 2),
                            'freeze_amount' => bcadd($userWallet->freeze_amount, $redpacket_log['amount'], 2)
                        ];
                        $freezeAmount = $redpacket_log['amount'];
                        $res = UserWallet::query()->where('user_id', $this->data['user_id'])->update($updateData);
                        $userWallet->available_amount = $updateData['available_amount'];
                        $userWallet->freeze_amount = $updateData['freeze_amount'];
                        if($res === false){
                            $connection->rollback();
                            $log->error('合并订单，扣除用户红包金额出错', $this->data);
                            throw new ErrorException('合并订单，扣除用户红包金额出错', 500);
                        }
                        $res = UserWalletLog::createWalletLog(
                            $this->data['user_id'],
                            -$redpacket_log['amount'],
                            $freezeAmount ?? 0,
                            $change_type,
                            trans($change_type_lang),
                            null,
                            $this->data['id'],
                            0,
                            0,
                            0,
                            $this->data['thread_id'] ?? 0
                        );
                        if($res === false){
                            $connection->rollback();
                            $log->error('合并订单，扣除红包，记录用户钱包明细出错', $this->data);
                            throw new ErrorException('合并订单，扣除红包，记录用户钱包明细出错', 500);
                        }
                    }
                    if(!empty($reward_log)){
                        $change_type = UserWalletLog::TYPE_QUESTION_REWARD_FREEZE;
                        $change_type_lang = 'wallet.question_reward_freeze';
                        $updateData = [
                            'available_amount' => bcsub($userWallet->available_amount, $reward_log['amount'], 2),
                            'freeze_amount' => bcadd($userWallet->freeze_amount, $reward_log['amount'], 2)
                        ];
                        $freezeAmount = $reward_log['amount'];
                        $res = UserWallet::query()->where('user_id', $this->data['user_id'])->update($updateData);
                        if($res === false){
                            $connection->rollback();
                            $log->error('合并订单，扣除用户悬赏金额出错', $this->data);
                            throw new ErrorException('合并订单，扣除用户悬赏金额出错', 500);
                        }
                        $res = UserWalletLog::createWalletLog(
                            $this->data['user_id'],
                            -$reward_log['amount'],
                            $freezeAmount ?? 0,
                            $change_type,
                            trans($change_type_lang),
                            null,
                            $this->data['id'],
                            0,
                            0,
                            0,
                            $this->data['thread_id'] ?? 0
                        );
                        if($res === false){
                            $connection->rollback();
                            $log->error('合并订单，扣除悬赏，记录用户钱包明细出错', $this->data);
                            throw new ErrorException('合并订单，扣除悬赏，记录用户钱包明细出错', 500);
                        }
                    }
                    break;
                default:
                    $change_type = $this->data['type'];
                    $change_type_lang = '';
            }

            //合并订单的情况，前面单独处理了，这里就不处理了
            if($this->data['type'] != Order::ORDER_TYPE_MERGE){
                $res = UserWallet::query()->where('user_id', $this->data['user_id'])->update($updateData);
                if($res === false){
                    $connection->rollback();
                    $log->error('钱包支付出错', $this->data);
                    throw new ErrorException('钱包支付出错', 500);
                }
                $res = UserWalletLog::createWalletLog(
                    $this->data['user_id'],
                    -$this->data['amount'],
                    $freezeAmount ?? 0,
                    $change_type,
                    trans($change_type_lang),
                    null,
                    $this->data['id'],
                    0,
                    0,
                    0,
                    $this->data['thread_id'] ?? 0
                );
                if($res === false){
                    $connection->rollback();
                    $log->error('钱包支付，增加用户钱包记录明细出错', $this->data);
                    throw new ErrorException('钱包支付，增加用户钱包记录明细出错', 500);
                }
            }


            // 支付成功处理
            $order_info = $this->paymentSuccess($payment_sn, $trade_no, $setting, $events);

            if ($order_info) {
                $events->dispatch(
                    new Updated($order_info)
                );
            }
            $connection->commit();
            if ($order_info) {
                app('payLog')->info("钱包支付成功,用户id:{$this->data['user_id']}");
                return [
                    'wallet_pay' => [
                        'result' => 'success',
                        'message' => trans('trade.wallet_pay_success'),
                    ]
                ];
            }
        } catch (Exception $e) {
            //回滚事务
            $connection->rollback();
            throw new ErrorException($e->getMessage(), 500);
        }
    }
}
