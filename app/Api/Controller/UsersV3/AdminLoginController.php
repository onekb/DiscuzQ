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

use App\Commands\Users\GenJwtToken;
use App\Common\ResponseCode;
use App\Events\Users\Logind;
use App\Models\User;
use Discuz\Base\DzqLog;
use Exception;
use App\Passport\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Discuz\Foundation\Application;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as Events;

class AdminLoginController extends DzqController
{
    protected $bus;
    protected $app;
    protected $events;

    public function __construct(Dispatcher $bus, Application $app, Events $events)
    {
        $this->bus = $bus;
        $this->app = $app;
        $this->events = $events;
    }

    protected function checkRequestPermissions(\App\Repositories\UserRepository $userRepo)
    {
        return true;
    }
    /**
     * @return array|mixed
     */
    public function main()
    {
        $data = [
            'username' => $this->inPut('username'),
            'password' => $this->inPut('password'),
        ];

        $this->dzqValidate($data, [
            'username' => 'required',
            'password' => 'required',
        ]);

        try {
            $response = $this->bus->dispatch(
                new GenJwtToken($data)
            );
        } catch (Exception $e) {
            DzqLog::error('admin_login_error', $data, $e->getMessage());
            if (empty($e->getMessage())) {
                return $this->outPut(ResponseCode::USERNAME_OR_PASSWORD_ERROR);
            }
            if ((int)$e->getMessage() > 0) {
                return $this->outPut(ResponseCode::LOGIN_FAILED,'登录失败，您还可以尝试'.(int)$e->getMessage().'次');
            } else {
                return $this->outPut(ResponseCode::LOGIN_FAILED,'登录错误次数超出限制');
            }
        }

        if($response->getStatusCode() != 200) {
            return $this->outPut(ResponseCode::LOGIN_FAILED);
        }

        $user = $this->app->make(UserRepository::class)->getUser();
        if (! $user->isAdmin()) {
            return $this->outPut(ResponseCode::UNAUTHORIZED);
        }
        $this->events->dispatch(new Logind($user));

        $accessToken = json_decode($response->getBody());
        $accessToken = array_merge($this->camelData(collect($accessToken)),['id' => $user->id]);
        return $this->outPut(ResponseCode::SUCCESS,'',$accessToken);
    }
}
