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
use App\Models\MobileCode;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Settings\SettingsRepository;
use Discuz\Auth\Exception\NotAuthenticatedException;
use Discuz\Base\DzqLog;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Database\ConnectionInterface;

class SmsRebindController extends AuthBaseController
{
    public $connection;
    public $settings;

    public function __construct(
        ConnectionInterface $connection,
        SettingsRepository  $settings
    ){
        $this->connection   = $connection;
        $this->settings     = $settings;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            throw new NotAuthenticatedException();
        }
        return true;
    }

    public function main()
    {
        $this->connection->beginTransaction();
        try {
            $mobileCode = $this->getMobileCode('rebind');

            $actor = User::query()->where('mobile', $mobileCode->mobile)->lockForUpdate()->first();
            if (empty($this->user->mobile)) {
                $this->connection->rollback();
                $this->outPut(ResponseCode::PC_REBIND_ERROR);
            }
            if ($mobileCode->mobile == $this->user->mobile) {
                $this->connection->rollback();
                $this->outPut(ResponseCode::NOT_REBIND_SELF_MOBILE);
            }
            if (!empty($actor)) {
                $this->connection->rollback();
                $this->outPut(ResponseCode::MOBILE_IS_ALREADY_BIND);
            }

            // 删除验证身份的验证码
            $result = MobileCode::query()   ->where('mobile', $this->user->getRawOriginal('mobile'))
                ->where('type', 'verify')
                ->where('state', 1)
                ->where('updated_at', '<', Carbon::now()->addMinutes(10))
                ->delete();

            if ($result < 1) {
                $this->connection->rollback();
                $this->outPut(ResponseCode::ORIGINAL_USER_MOBILE_VERIFY_ERROR, '', []);
            }

            $this->user->changeMobile($mobileCode->mobile);
            $this->user->save();

            $this->connection->commit();
            $this->outPut(ResponseCode::SUCCESS, '', []);
        } catch (Exception $e) {
            DzqLog::error('sms_rebind_api_error', [
                'mobile'  => $this->inPut('mobile'),
                'code'    => $this->inPut('code')
            ], $e->getMessage());
            $this->connection->rollback();
            $this->outPut(ResponseCode::INTERNAL_ERROR, '手机号换绑接口异常');
        }
    }
}
