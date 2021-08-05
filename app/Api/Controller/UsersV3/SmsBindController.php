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

use App\Common\AuthUtils;
use App\Common\ResponseCode;
use App\Models\SessionToken;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Settings\SettingsRepository;
use Discuz\Base\DzqLog;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\ConnectionInterface;

class SmsBindController extends AuthBaseController
{
    public $bus;
    public $connection;
    public $settings;

    public function __construct(
        Dispatcher          $bus,
        ConnectionInterface $connection,
        SettingsRepository  $settings
    ){
        $this->bus          = $bus;
        $this->connection   = $connection;
        $this->settings     = $settings;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $this->info('begin_sms_bind_process');
        $this->connection->beginTransaction();
        try {
            $paramData = [
                'mobile'        => $this->inPut('mobile'),
                'code'          => $this->inPut('code'),
                'sessionToken'  => $this->inPut('sessionToken')
            ];

            $mobileCode     = $this->getMobileCode('bind');
            $sessionToken   = $this->inPut('sessionToken');
            $token          = SessionToken::get($sessionToken);
            $actor          = !empty($token->user) ? $token->user : $this->user;

            $actor = User::query()->lockForUpdate()->find($actor->id);

            $this->info('get_token_with_session_token', [
                'input'      => [
                    'sessionToken' => $sessionToken
                ],
                'output'      => [
                    'token'    => $token,
                    'actor'    => $actor
                ]
            ]);

            if (empty($actor) || $actor->isGuest()) {
                $this->connection->rollback();
                $this->outPut(ResponseCode::JUMP_TO_LOGIN);
            }

            if (!empty($actor->mobile)) {
                $this->connection->rollback();
                $this->outPut(ResponseCode::BIND_ERROR,'用户已绑定了手机号');
            }

            $exists = User::query()->where('mobile', $mobileCode->mobile)->exists();
            if ($exists) {
                $this->connection->rollback();
                $this->outPut(ResponseCode::MOBILE_IS_ALREADY_BIND);
            }

            $actor->changeMobile($mobileCode->mobile);
            $actor->save();

            $this->updateUserBindType($actor, AuthUtils::PHONE);

            //用于用户名登录绑定手机号使用
            if (!empty($token->user)) {
                if (empty($token->user->username)) {
                    $this->connection->rollback();
                    $this->outPut(ResponseCode::USERNAME_NOT_NULL);
                }

                $accessToken = $this->getAccessToken($token->user);
                $result = $this->camelData(collect($accessToken));
                $result = $this->addUserInfo($token->user, $result);
                $this->connection->commit();
                $this->outPut(ResponseCode::SUCCESS, '', $result);
            }
            $this->connection->commit();
            $this->outPut(ResponseCode::SUCCESS, '', []);

        } catch (Exception $e) {
            DzqLog::error('sms_bind_api_error', $paramData, $e->getMessage());
            $this->connection->rollback();
            $this->outPut(ResponseCode::INTERNAL_ERROR, '手机号绑定接口异常');
        }
    }
}
