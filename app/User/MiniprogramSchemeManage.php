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

namespace App\User;

use App\Settings\SettingsRepository;
use Carbon\Carbon;
use Discuz\Base\DzqLog;
use Discuz\Wechat\EasyWechatTrait;
use GuzzleHttp\Client;

use App\Models\MiniprogramSchemeManage as Scheme;

class MiniprogramSchemeManage
{
    use EasyWechatTrait;
    /**
     * 微信小程序全局scheme获取url
     * @var string
     */
    protected $miniProgramSchemeUrl = 'https://api.weixin.qq.com/wxa/generatescheme';
    protected $httpClient;
    protected $settingsRepository;
    protected $schemeManage;

    public function __construct(SettingsRepository $settingsRepository, Scheme $schemeManage)
    {
        $this->settingsRepository = $settingsRepository;
        $this->httpClient = new Client();
        $this->schemeManage = $schemeManage;
    }


    /**
     * get wx scheme
     * @param $accessToken
     * @return string
     */
    public function getMiniProgramSchemeRefresh($accessToken): string
    {
        $url = $this->miniProgramSchemeUrl.'?access_token='.$accessToken;

        $expiredTime = Carbon::now()->getTimestamp() + 30*24*3600;

        $wxSchemeResponse = $this->httpClient->post($url, [
            'json' => [
                'is_expire' => true,
                'expire_time'  => $expiredTime
            ]
        ]);
        $wxScheme = json_decode($wxSchemeResponse->getBody()->getContents(),true);

        if(isset($wxScheme['errcode']) && $wxScheme['errcode'] != 0 && isset($wxScheme['errmsg'])) {
            DzqLog::error('gen_scheme_error', [
                'errcode'   => $wxScheme['errcode'],
                'errmsg'    => $wxScheme['errmsg']
            ]);
            return 'gen_scheme_error';
        }
        $appId = $this->settingsRepository->get('miniprogram_app_id', 'wx_miniprogram');
        Scheme::createRecord($wxScheme['openlink'], $appId, $expiredTime);
        return $wxScheme['openlink'];
    }

    /**
     * get wx bindscheme
     * @param $accessToken
     * @param $path
     * @param $query
     * @return string
     */
    public function getMiniProgramBindSchemeRefresh($accessToken, $path, $query): string
    {
        $url = $this->miniProgramSchemeUrl.'?access_token='.$accessToken;

        $wxSchemeResponse = $this->httpClient->post($url, [
            'json' => [
                'jump_wxa' => [
                    'path' => $path,
                    'query' => $query
                ]
            ]
        ]);
        $wxScheme = json_decode($wxSchemeResponse->getBody()->getContents(),true);

        if(isset($wxScheme['errcode']) && $wxScheme['errcode'] != 0 && isset($wxScheme['errmsg'])) {
            DzqLog::error('gen_bind_scheme_error', [
                'path'      => $path,
                'query'     => $query,
                'errcode'   => $wxScheme['errcode'],
                'errmsg'    => $wxScheme['errmsg']
            ]);
            return 'gen_bind_scheme_error';
        }
        return $wxScheme['openlink'];
    }

    public function getMiniProgramScheme(): string
    {
        $record = !empty(Scheme::getLastRecord()) ? Scheme::getLastRecord()->toArray() : '';
        if(empty($record) || $record['expired_at'] < Carbon::now()->getTimestamp()) {
            return '';
        }
        return $record['scheme'];
    }
}
