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

class MiniProgramPcBindPollController extends AuthBaseController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        try {
            $token = $this->getScanCodeToken();

            if (isset($token->payload['bind']) && $token->payload['bind']) {
                $result = $this->camelData($token->payload);
                $result = $this->addUserInfo($token->user, $result);
                // 绑定成功
                $this->outPut(ResponseCode::SUCCESS, '', $result);
            }

            $this->outPut(ResponseCode::PC_BIND_ERROR, '请刷新二维码重新绑定');
        } catch (\Exception $e) {
            DzqLog::error('mini_program_pc_bind_poll_api_error', [
                'sessionToken' => $this->inPut('sessionToken')
            ], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, 'pc端小程序绑定轮询接口异常');
        }
    }
}
