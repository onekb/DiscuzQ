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

namespace App\Api\Controller\Oauth2V3;

use App\Api\Serializer\TokenSerializer;
use App\Common\ResponseCode;
use App\Passport\Repositories\AccessTokenRepository;
use App\Passport\Repositories\RefreshTokenRepository;
use App\Repositories\UserRepository;
use DateInterval;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Discuz\Foundation\Application;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laminas\Diactoros\Response;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Psr\Http\Message\ServerRequestInterface;

class RefreshTokenController extends DzqController
{
    public $serializer = TokenSerializer::class;

    protected $app;

    protected $events;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有刷新token的权限!');
        }
        return true;
    }

    public function __construct(Application $app, Dispatcher $events, ServerRequestInterface $request)
    {
        $this->app     = $app;
        $this->events  = $events;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function main()
    {
        $refreshToken = $this->input('refreshToken');
        $request = $this->request;
        if (empty($refreshToken)) {
            return $this->outPut(ResponseCode::INVALID_PARAMETER, 'refreshToken不能为空');
        }

        $refreshTokenRepository = new RefreshTokenRepository();

        // Setup the authorization server
        $server = $this->app->make(AuthorizationServer::class);

        $grant = new RefreshTokenGrant($refreshTokenRepository);
        // new refresh tokens will expire after 1 month
        $grant->setRefreshTokenTTL(new DateInterval(AccessTokenRepository::REFER_TOKEN_EXP)); 

        // Enable the refresh token grant on the server
        $server->enableGrantType(
            $grant,
            new DateInterval(AccessTokenRepository::TOKEN_EXP)
        ); // new access tokens will expire after an hour

        if ($request->getParsedBody() instanceof Collection) {
            $request = $request->withParsedBody(
                [
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'client_id'     => '',
                    'client_secret' => '',
                    'scope'         => '',
                ]
            );
        }
        try {
            $response = $server->respondToAccessTokenRequest($request, new Response());
            $result = json_decode((string)$response->getBody(), true);
            return $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($result));
        } catch (Exception $e) {
            return $this->outPut(ResponseCode::INTERNAL_ERROR, $e->getMessage());
        }
    }
}
