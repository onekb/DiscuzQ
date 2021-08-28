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
use App\User\MiniprogramSchemeManage;
use Discuz\Base\DzqLog;
use Discuz\Wechat\EasyWechatTrait;
use GuzzleHttp\Client;

class MiniProgramBindSchemeGenController extends AuthBaseController
{
    use EasyWechatTrait;

    /**
     * scheme跳转路由类型
     * @var string[]
     */
    static $schemeType = [
        'bind_mini'
    ];
    /**
     * scheme跳转路由类型与路由映射
     * @var string[]
     */
    //todo 对接前端时更换路由
    static $schemeTypeAndRouteMap = [
        'bind_mini'  => 'subPages/user/wx-bind/index'    // 绑定
    ];

    protected $httpClient;
    protected $manage;
    public function __construct(MiniprogramSchemeManage $manage)
    {
        $this->httpClient = new Client();
        $this->manage = $manage;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        try {
            $type = $this->inPut('type');
            $query = !empty($this->inPut('query')) ? $this->inPut('query') : [];
            if(! in_array($type, self::$schemeType)) {
                $this->outPut(ResponseCode::GEN_SCHEME_TYPE_ERROR);
            }
            if (empty($query['scene'])) {
                if (! $this->user->isGuest()) {
                    $accessToken = $this->getAccessToken($this->user);
                    $token = SessionToken::generate(
                        SessionToken::WECHAT_MINIPROGRAM_SCHEME_BIND,
                        $accessToken,
                        $this->user->id
                    );
                    $token->save();
                    $query['scene'] = $token->token;
                } else {
                    $this->outPut(ResponseCode::INVALID_PARAMETER, '用户不存在', ['id' => $this->user->id]);
                }
            }

            //跳转路由选择
            $path = self::$schemeTypeAndRouteMap[$type];
            $app = $this->miniProgram();
            $globalAccessToken = $app->access_token->getToken(true);
            if(! isset($globalAccessToken['access_token'])) {
                //todo 记录错误日志
                DzqLog::error('mini_program_bind_scheme_gen_api_error', [
                    'globalAccessToken' => $globalAccessToken
                ]);
                $this->outPut(ResponseCode::MINI_PROGRAM_GET_ACCESS_TOKEN_ERROR);
            }
            $miniProgramScheme = $this->manage->getMiniProgramBindSchemeRefresh(
                $globalAccessToken['access_token'],
                $path,
                http_build_query($query)
            );
            if($miniProgramScheme == 'gen_bind_scheme_error') {
                $this->outPut(ResponseCode::GEN_BIND_SCHEME_TYPE_ERROR, '', [
                    'path'          => $path,
                    'query'         => http_build_query($query)
                ]);
            }

            $data['openLink'] = $miniProgramScheme;
            $this->outPut(ResponseCode::SUCCESS, '', $data);
        } catch (\Exception $e) {
            DzqLog::error('mini_program_bind_scheme_gen_api_error', [
                'type' => $this->inPut('type'),
                'query' => $this->inPut('query')
            ], $e->getMessage());
            $this->outPut(ResponseCode::INTERNAL_ERROR, '小程序BindSchemeGen接口异常', [$e->getMessage()]);
        }
    }
}
