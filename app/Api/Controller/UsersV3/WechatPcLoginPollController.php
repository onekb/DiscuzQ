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

class WechatPcLoginPollController extends AuthBaseController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        try {
            $token = $this->getScanCodeToken();

            if (isset($token->payload['code']) ) {
                if (empty($token->payload['code'])) {
                    // 扫码中
                    $this->outPut(ResponseCode::PC_QRCODE_ERROR);
                }
            }

            $data = $token->payload;
            $data['user_id'] = $token->user_id; // 用于序列化返回 user_id

            $result = $this->camelData($data);
            $result = $this->addUserInfo($token->user, $result);
            $this->outPut(ResponseCode::SUCCESS, '', $result);
        } catch (\Exception $e) {
            DzqLog::error('wechat_pc_bind_poll_api_error', [
                'sessionToken' => $this->inPut('sessionToken')
            ], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, 'pc、H5轮询登录接口异常');
        }
    }
}
