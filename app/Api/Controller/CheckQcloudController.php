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

namespace App\Api\Controller;

use App\Models\Setting;
use Discuz\Http\DiscuzResponseFactory;
use Discuz\Qcloud\QcloudStatisticsTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Ms\V20180408\Models\DescribeUserBaseInfoInstanceRequest;
use TencentCloud\Ms\V20180408\MsClient;

class CheckQcloudController implements RequestHandlerInterface
{
    use QcloudStatisticsTrait;

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Discuz\Auth\Exception\PermissionDeniedException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->uinStatis();
        $setting = Setting::query()->get()->toArray();
        $setting = array_column($setting, null, 'key');
        $qcloudSecretId = !empty($setting['qcloud_secret_id']) ? $setting['qcloud_secret_id']['value'] : '';
        $qcloudSecretKey = !empty($setting['qcloud_secret_key']) ? $setting['qcloud_secret_key']['value'] : '';
        $ret['data']['attributes']['isBuildQcloud'] = false;
        if(empty($qcloudSecretId) || empty($qcloudSecretKey)){
            return DiscuzResponseFactory::JsonResponse($ret);
        }
        $cred = new Credential($qcloudSecretId, $qcloudSecretKey);
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint('ms.tencentcloudapi.com');
        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new MsClient($cred, '', $clientProfile);
        $req = new DescribeUserBaseInfoInstanceRequest();
        $params = '{}';
        $req->fromJsonString($params);
        $resp = $client->DescribeUserBaseInfoInstance($req);
        if(empty($resp->UserUin)){
            return DiscuzResponseFactory::JsonResponse($ret);
        }
        $ret['data']['attributes']['isBuildQcloud'] = true;
        return  DiscuzResponseFactory::JsonResponse($ret);
    }
}
