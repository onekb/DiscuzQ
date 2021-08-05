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


use App\Models\SessionToken;
use App\Settings\SettingsRepository;
use App\User\Bound;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Database\ConnectionInterface;
use Discuz\Contracts\Socialite\Factory;
use Discuz\Auth\AssertPermissionTrait;
 abstract class AbstractWechatH5LoginBaseController extends AuthBaseController
{

    use AssertPermissionTrait;
    protected $socialite;
    protected $bus;
    protected $cache;
    protected $validation;
    protected $events;
    protected $settings;
    protected $bound;
    protected $db;

    public function __construct(
        Factory             $socialite,
        Dispatcher          $bus,
        Repository          $cache,
        ValidationFactory   $validation,
        Events              $events,
        SettingsRepository  $settings,
        Bound               $bound,
        ConnectionInterface $db
    ){
        $this->socialite    = $socialite;
        $this->bus          = $bus;
        $this->cache        = $cache;
        $this->validation   = $validation;
        $this->events       = $events;
        $this->settings     = $settings;
        $this->bound        = $bound;
        $this->db           = $db;
    }


     /**
      * 获取微信用户基本信息
      * @return mixed
      * @throws \Illuminate\Validation\ValidationException
      */
    public function getWxUser()
    {
        $code           = $this->inPut('code');
        $sessionId      = $this->inPut('sessionId');

        $request = $this->request->withAttribute('session', new SessionToken())->withAttribute('sessionId', $sessionId);

        $this->validation->make([
            'code'      => $code,
            'sessionId' => $sessionId,
        ], [
            'code'      => 'required',
            'sessionId' => 'required'
        ])->validate();

        $this->socialite->setRequest($request);

        $driver = $this->socialite->driver('wechat');
        return $driver->user();
    }

     protected function fixData($rawUser, $actor)
     {
         $data = array_merge($rawUser, ['user_id' => $actor->id ?: null, $this->getType() => $rawUser['openid']]);
         unset($data['openid'], $data['language']);
         $data['privilege'] = serialize($data['privilege']);
         return $data;
     }

     protected function getDriver()
     {
         return 'wechat';
     }

     protected function getType()
     {
         return 'mp_openid';
     }
}
