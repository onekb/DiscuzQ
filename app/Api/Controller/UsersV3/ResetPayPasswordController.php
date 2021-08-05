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

namespace App\Api\Controller\UsersV3;

use App\Common\ResponseCode;
use App\Models\SessionToken;
use App\Models\UserWalletFailLogs;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Discuz\Base\DzqController;
use Illuminate\Validation\Factory as Validator;

class ResetPayPasswordController extends DzqController
{
    /**
     * @var Validator
     */
    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $actor = $this->user;
        $request = $this->request;
//        $this->assertRegistered($actor);
        //验证错误次数
        $failCount = UserWalletFailLogs::query()
            ->where('user_id', $this->user->id)
            ->whereBetween('created_at', [Carbon::today(),Carbon::tomorrow()])
            ->count();

        if ($failCount > UserWalletFailLogs::TOPLIMIT) {
            $this->outPut(ResponseCode::INVALID_PARAMETER,'您输入的密码错误次数已超限，请点击忘记密码找回或次日后重试');
        }

        $payPassword = $this->inPut('payPassword');
        $this->validator->make(compact('payPassword'), [
            'payPassword' => [
                'bail',
                'required',
                'digits:6',
                function ($attribute, $value, $fail) use ($actor,$request,$failCount) {
                    // 验证支付密码
                    if (! $actor->checkWalletPayPassword($value)) {
                        //记录钱包密码错误日志
                        UserWalletFailLogs::build(ip($request->getServerParams()), $actor->id);
                        if (UserWalletFailLogs::TOPLIMIT == $failCount) {
                            $this->outPut(ResponseCode::INVALID_PARAMETER,'您输入的密码错误次数已超限，请点击忘记密码找回或次日后重试');
                        } else {
                            $this->outPut(ResponseCode::INVALID_PARAMETER,'支付密码错误,今天还能重试'.(UserWalletFailLogs::TOPLIMIT - $failCount)."次");
                        }
                    }
                }
            ],
        ])->validate();

        // 正确后清除错误记录
        if ($failCount > 0) {
            UserWalletFailLogs::deleteAll($this->user->id);
        }

        $token = SessionToken::generate('reset_pay_password', null, $this->user->id);
        $token->save();

        $this->outPut(ResponseCode::SUCCESS, '', [
            'sessionId' => $token->token
        ]);
    }
}
