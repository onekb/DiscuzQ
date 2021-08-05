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
use App\User\MiniprogramSchemeManage;
use Discuz\Base\DzqLog;
use Discuz\Wechat\EasyWechatTrait;
use GuzzleHttp\Client;

class MiniProgramSchemeGenController extends AuthBaseController
{
    use EasyWechatTrait;

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
            $scheme = $this->manage->getMiniProgramScheme();
            if(!empty($scheme)) {
                $this->outPut(ResponseCode::SUCCESS, '', ['openLink' => $scheme]);
            }
            $app = $this->miniProgram();
            $globalAccessToken = $app->access_token->getToken(true);
            if(! isset($globalAccessToken['access_token'])) {
                //todo 记录错误日志
                DzqLog::error('mini_program_scheme_gen_api_error', [
                    'globalAccessToken' => $globalAccessToken
                ]);
                return $this->outPut(ResponseCode::MINI_PROGRAM_GET_ACCESS_TOKEN_ERROR);
            }
            $miniProgramScheme = $this->manage->getMiniProgramSchemeRefresh($globalAccessToken['access_token']);
            if($miniProgramScheme == 'gen_scheme_error') {
                return $this->outPut(ResponseCode::MINI_PROGRAM_QR_CODE_ERROR);
            }

            $data['openLink'] = $miniProgramScheme;
            $this->outPut(ResponseCode::SUCCESS, '', $data);
        } catch (\Exception $e) {
            DzqLog::error('mini_program_scheme_gen_api_error', [
                'globalAccessToken' => $globalAccessToken
            ], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '小程序SchemeGen接口异常');
        }
    }
}
