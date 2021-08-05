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
use App\Models\SessionToken;
use App\Passport\Repositories\UserRepository;
use Discuz\Foundation\Application;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Validation\Factory as Validator;
abstract class AbstractLoginBaseController extends AuthBaseController
{

    protected $bus;

    protected $app;

    protected $events;

    protected $type;

    protected $validator;

    public function __construct(
        Dispatcher $bus,
        Application $app,
        Events $events,
        Validator $validator
    )
    {
        $this->bus = $bus;
        $this->app = $app;
        $this->events = $events;
        $this->validator = $validator;
    }

    public function main()
    {
        $data = [
            'username' => $this->inPut('username'),
            'password' => $this->inPut('password'),
        ];

        $this->validator->make($data, [
            'username' => 'required',
            'password' => 'required',
        ])->validate();

        $response = $this->bus->dispatch(
            new GenJwtToken($data)
        );

        $accessToken = json_decode($response->getBody(), true);

        if ($response->getStatusCode() === 200) {
            $user = $this->app->make(UserRepository::class)->getUser();

            $this->events->dispatch(new Logind($user));
        }
        if($this->type == 'username_login') {
            return $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($accessToken));
        }
        //手机浏览器登录，需要做绑定前准备
        $token = SessionToken::generate(SessionToken::WECHAT_MOBILE_BIND, $accessToken , $user->id);
        $data = array_merge($this->camelData($accessToken),['sessionToken' => $token->token]);
        $token->save();
        return $this->outPut(ResponseCode::SUCCESS, '', $data);
    }

}
