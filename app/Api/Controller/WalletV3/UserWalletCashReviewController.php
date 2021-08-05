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

namespace App\Api\Controller\WalletV3;

use App\Common\ResponseCode;
use App\Events\Wallet\Cash;
use App\Models\UserWallet;
use App\Models\UserWalletCash;
use App\Models\UserWalletLog;
use App\Repositories\UserRepository;
use App\Settings\SettingsRepository;
use App\Trade\Config\GatewayConfig;
use Carbon\Carbon;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserWalletCashReviewController extends DzqController
{
    private $settings;
    private $data;
    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    public $events;
    public $db;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(SettingsRepository $settings, Dispatcher $events)
    {
        $this->settings = $settings;
        $this->events = $events;
        $this->db = app('db');
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $this->ip_address = ip($this->request->getServerParams());
        $this->data = [
            'ids'           => (array) $this->inPut('ids'),
            'cashStatus'    => (int) $this->inPut('cashStatus'),
            'remark'        => $this->inPut('remark'),
        ];
        $log = app('payLog');
        $log_data = $this->data;
        $log->info("requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：",$log_data);
        $this->dzqValidate($this->data,[
            'ids'           => 'required|array',
            'cashStatus'    => 'required|int',
            'remark'        => 'sometimes|string|max:255',
        ]);

        //只允许修改为审核通过或审核不通过
        if (!in_array($this->data['cashStatus'], [UserWalletCash::STATUS_REVIEWED, UserWalletCash::STATUS_REVIEW_FAILED])) {
            $log->error("非法操作 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
            return $this->outPut(ResponseCode::NET_ERROR, '非法操作');
        }
        $ip = ip($this->request->getServerParams());
        $cash_status    = $this->data['cashStatus'];
        $status_result  = []; //结果数组
        //是否开启企业打款
        $wxpay_mchpay_close = (bool)$this->settings->get('wxpay_mchpay_close', 'wxpay');
        $db = $this->db;
        $collection = collect($this->data['ids'])
            ->unique()
            ->map(function ($id) use ($cash_status, &$status_result, $wxpay_mchpay_close, $ip, $db, $log, $log_data) {
                $db->beginTransaction();
                //取出待审核数据
                $cash_record = UserWalletCash::find($id);
                //只允许修改未审核的数据。
                if (empty($cash_record) || $cash_record->cash_status != UserWalletCash::STATUS_REVIEW) {
                    $db->rollBack();
                    $log->error("只允许修改未审核的数据 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                    return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                }
                $cash_record->cash_status = $cash_status;
                if ($cash_status == UserWalletCash::STATUS_REVIEW_FAILED) {
                    $cash_apply_amount = $cash_record->cash_apply_amount;//提现申请金额
                    //审核不通过解冻金额
                    $user_id = $cash_record->user_id;
                    //开始事务
                    try {
                        //获取用户钱包
                        $user_wallet = UserWallet::lockForUpdate()->find($user_id);
                        //返回冻结金额至用户钱包
                        $user_wallet->freeze_amount    = $user_wallet->freeze_amount - $cash_apply_amount;
                        $user_wallet->available_amount = $user_wallet->available_amount + $cash_apply_amount;
                        $res = $user_wallet->save();
                        if($res === false){
                            $db->rollBack();
                            $log->error("修改用户冻结金额、可用金额出错 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                            return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                        }
                        //冻结变动金额，为负数数
                        $change_freeze_amount = -$cash_apply_amount;
                        //可用金额增加
                        $change_available_amount = $cash_apply_amount;
                        //添加钱包明细
                        $res = UserWalletLog::createWalletLog(
                            $user_id,
                            $change_available_amount,
                            $change_freeze_amount,
                            UserWalletLog::TYPE_CASH_THAW,
                            app('translator')->get('wallet.cash_review_failure'),
                            $cash_record->id
                        );
                        if(!$res){
                            $db->rollBack();
                            $log->error("添加钱包明细 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                            return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                        }
                        $cash_record->remark = Arr::get($this->data, 'remark', '');
                        $cash_record->refunds_status = UserWalletCash::REFUNDS_STATUS_YES;
                        $res = $cash_record->save();
                        if($res === false){
                            $db->rollBack();
                            $log->error("修改提现记录状态出错 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                            return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                        }
                        $db->commit();
                        return $status_result[$id] = 'success';
                    } catch (\Exception $e) {
                        //回滚事务
                        $db->rollback();
                        $log->error("审核出错 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", [$log_data, $e->getTraceAsString()]);
                        return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                    }
                }

                // 审核通过，判断后台是否开了微信自动打款，如果开了就走自动打款，否则直接已打款，站长线下打款
                if($wxpay_mchpay_close){        //开通了企业打款
                    try {
                        //检查证书
                        if (!file_exists(storage_path().'/cert/apiclient_cert.pem') || !file_exists(storage_path().'/cert/apiclient_key.pem')) {
                            $db->rollBack();
                            $log->error("检查证书失败 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                            return $this->outPut(ResponseCode::INTERNAL_ERROR, '证书不存在，请先上传证书！');
                        }
                        $cash_record->cash_type = UserWalletCash::TRANSFER_TYPE_MCH;
                        $cash_record->cash_status = UserWalletCash::STATUS_IN_PAYMENT;
                        $res = $cash_record->save();
                        if($res === false){
                            $db->rollBack();
                            $log->error("修改提现记录出错 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                            return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                        }
                        //触发提现钩子事件
                        $this->events->dispatch(
                            new Cash($cash_record, $ip, GatewayConfig::WECAHT_TRANSFER)
                        );
                        $db->commit();
                        $log->info("requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                        return $status_result[$id] = 'success';
                    }catch (\Exception $e){
                        $db->rollBack();
                        $log->error("审核出错 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", [$log_data, $e->getTraceAsString()]);
                        return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                    }
                }else{          //没有开通企业打款，直接扣款
                    try {
                        $cash_record->cash_type = UserWalletCash::TRANSFER_TYPE_MANUAL;
                        $cash_record->remark = Arr::get($this->data, 'remark', '');
                        $cash_record->cash_status = UserWalletCash::STATUS_PAID;//已打款
                        $cash_record->trade_time = Carbon::now();
                        $res = $cash_record->save();
                        if($res === false){
                            $db->rollBack();
                            $log->error("修改提现记录出错 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                            return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                        }
                        //获取用户钱包
                        $user_wallet = UserWallet::lockForUpdate()->find($cash_record->user_id);
                        //去除冻结金额
                        $user_wallet->freeze_amount = $user_wallet->freeze_amount - $cash_record->cash_apply_amount;
                        $res = $user_wallet->save();
                        if($res === false){
                            $db->rollBack();
                            $log->error("修改用户冻结金额、可用金额出错 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                            return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                        }
                        //冻结变动金额，为负数
                        $change_freeze_amount = -$cash_record->cash_apply_amount;
                        //添加钱包明细
                        $res = UserWalletLog::createWalletLog(
                            $cash_record->user_id,
                            0,
                            $change_freeze_amount,
                            UserWalletLog::TYPE_CASH_SUCCESS,
                            app('translator')->get('wallet.cash_success'),
                            $cash_record->id
                        );
                        if($res === false){
                            $db->rollBack();
                            $log->error("添加钱包明细出错 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                            return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                        }
                        $db->commit();
                        $log->info("requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", $log_data);
                        return $status_result[$id] = 'success';
                    }catch (\Exception $e){
                        $db->rollBack();
                        $log->error("审核出错 requestId：{$this->requestId}，user_id：{$this->user->id}，request_data：", [$log_data, $e->getTraceAsString()]);
                        return $this->outPut(ResponseCode::INTERNAL_ERROR, '提现审核失败！');
                    }
                }
            });

        return $this->outPut(ResponseCode::SUCCESS, '', $status_result);
    }
}
