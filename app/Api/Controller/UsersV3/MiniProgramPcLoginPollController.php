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
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Discuz\Base\DzqLog;

class MiniProgramPcLoginPollController extends AuthBaseController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        try {
            $token = $this->getScanCodeToken();
            if (isset($token->payload['code'])) {
                if (empty($token->payload['code'])) {
                    // 扫码中
                    $this->outPut(ResponseCode::PC_QRCODE_ERROR);
                } else {
                    /*$noUserException = new NoUserException();
                    $noUserException->setToken((object) $token->payload['token']);
                    $noUserException->setUser((object) $token->payload['user']);
                    $token->payload['rebind'] && $noUserException->setCode('rebind_mp_wechat');

                    throw $noUserException;*/
                    //todo 增加code 存在逻辑
                }
            }

            $data = $token->payload;
            $data['user_id'] = $token->user_id; // 用于序列化返回 user_id

            $result = $this->camelData($data);

            $result = $this->addUserInfo( $token->user, $result);

            $this->outPut(ResponseCode::SUCCESS, '', $result);
        } catch (\Exception $e) {
            DzqLog::error('mini_program_pc_login_poll_api_error', [
                'sessionToken' => $this->inPut('sessionToken')
            ], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, 'pc端小程序登录轮询接口异常');
        }
    }
}
