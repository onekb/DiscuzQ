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

namespace App\Commands\Wallet;

use App\Common\ResponseCode;
use App\Exceptions\WalletException;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use App\Models\AdminActionLog;
use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;

class UpdateUserWallet
{

    public $user_id;

    /**
     * 执行操作的用户.
     *
     * @var User
     */
    public $actor;

    /**
     * 请求的数据.
     *
     * @var array
     */
    public $data;

    /**
     * 初始化命令参数
     * @param User   $actor        执行操作的用户.
     * @param array  $data         请求的数据.
     */
    public function __construct($user_id, $actor, $data)
    {
        $this->user_id = $user_id;
        $this->actor   = $actor;
        $this->data    = $data;

    }

    /**
     * 执行命令
     * @return model UserWallet
     * @throws Exception
     */
    public function handle(ConnectionInterface $db)
    {
        $operate_type   = Arr::get($this->data, 'operateType');
        $operate_amount = Arr::get($this->data, 'operateAmount');
        $operate_reason = Arr::get($this->data, 'operateReason', '');
        $operate_reason = trim($operate_reason);
        $wallet_status  = Arr::get($this->data, 'walletStatus');
        if (!empty($operate_type)) {
            if (!in_array($operate_type, [UserWallet::OPERATE_INCREASE, UserWallet::OPERATE_DECREASE])) {
                throw new WalletException(trans('wallet.operate_type_error'));
            }
        }

        if (!is_null($wallet_status) && !in_array($wallet_status, [UserWallet::WALLET_STATUS_NORMAL, UserWallet::WALLET_STATUS_FROZEN])) {
            throw new WalletException(trans('wallet.wallet_status_error'));
        }
        //操作金额
        $change_available_amount = sprintf('%.2f', floatval($operate_amount));
        //开始事务
        $db->beginTransaction();
        $change_type = '';
        $old_wallet_status = UserWallet::query()->where('user_id', $this->user_id)->first();

        try {
            $user_wallet = UserWallet::lockForUpdate()->findOrFail($this->user_id);
            switch ($operate_type) {
                case UserWallet::OPERATE_INCREASE: //增加
                    $change_type = UserWalletLog::TYPE_INCOME_ARTIFICIAL;
                    if (!strlen($operate_reason)) {
                        $operate_reason = app('translator')->get('wallet.income_artificial');
                    }
                    break;
                case UserWallet::OPERATE_DECREASE: //减少
                    if ($user_wallet->available_amount - $operate_amount < 0) {
                        throw new Exception(trans('wallet.available_amount_error'));
                    }
                    if (!strlen($operate_reason)) {
                        $operate_reason = app('translator')->get('wallet.expend_artificial');
                    }
                    $change_available_amount = -$change_available_amount;
                    $change_type = UserWalletLog::TYPE_EXPEND_ARTIFICIAL;
                    break;
                default:
                    break;
            }
            //钱包状态修改
            if (!is_null($wallet_status)) {
                $user_wallet->wallet_status = (int) $wallet_status;
            }
            //金额变动
            if ($change_type) {
                //修改钱包金额
                $user_wallet->available_amount = sprintf('%.2f', ($user_wallet->available_amount + $change_available_amount));
                //添加钱包明细
                $user_wallet_log = UserWalletLog::createWalletLog(
                    $this->user_id,
                    $change_available_amount,
                    0,
                    $change_type,
                    $operate_reason
                );
            }
            $user_wallet->save();

            $userDetail = User::query()->where('id', $this->user_id)->first();
            
            if($operate_amount !== '' && $operate_amount > 0){
                if($change_type == 32){
                    $desc = '增加';
                }else{
                    $desc = '减少';
                }

                AdminActionLog::createAdminActionLog(
                    $this->actor->id,
                    $desc . '了用户【'. $userDetail['username'] .'】的余额'. $operate_amount .'元'
                );
            }
            

            if($old_wallet_status['wallet_status'] !== $user_wallet->wallet_status){
                if($user_wallet->wallet_status === 1){
                    $status_desc = '冻结';
                }else{
                    $status_desc = '恢复';
                }

                AdminActionLog::createAdminActionLog(
                    $this->actor->id,
                    $status_desc . '了用户【'. $userDetail['username'] .'】提现'
                );
                
            }

            //提交事务
            $db->commit();
            return $user_wallet;
        } catch (Exception $e) {
            //回滚事务
            $db->rollback();
            \Discuz\Common\Utils::outPut(ResponseCode::INTERNAL_ERROR, $e->getMessage());
        }
    }
}
