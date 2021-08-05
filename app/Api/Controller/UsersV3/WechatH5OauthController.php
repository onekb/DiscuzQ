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
use App\Traits\RequestContainerTrait;
use Discuz\Base\DzqLog;
use Discuz\Contracts\Socialite\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class WechatH5OauthController implements RequestHandlerInterface
{
    use RequestContainerTrait;

    public $type = 'wechat';

    protected $socialite;

    public function __construct(Factory $socialite)
    {
        $this->socialite = $socialite;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $this->setSiteRequest($request);

            $request = $request->withAttribute('session', new SessionToken());
            $this->socialite->setRequest($request);
            return $this->socialite->driver($this->type)->redirect();
        } catch (\Exception $e) {
            DzqLog::error('wechat_h5_oauth_api_error', [
                'request'   =>  $request
            ], $e->getMessage());
            \Discuz\Common\Utils::outPut(ResponseCode::INTERNAL_ERROR, 'H5授权接口异常');
        }
    }
}
