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
use App\Repositories\UserRepository;
use Discuz\Base\DzqLog;

class SmsVerifyController extends AuthBaseController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        try {
            $mobileCode = $this->getMobileCode('verify');

            if ($mobileCode->user->exists) {
                $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($mobileCode->user));
            }

            $this->outPut(ResponseCode::NOT_FOUND_USER);
        } catch (\Exception $e) {
            DzqLog::error('sms_verify_api_error', [
                'mobile'    => $this->inPut('mobile'),
                'code'      => $this->inPut('code')
            ], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '手机号验证接口异常');
        }
    }
}
