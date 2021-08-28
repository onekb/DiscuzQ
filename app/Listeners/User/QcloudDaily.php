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

namespace App\Listeners\User;

use App\Models\Setting;
use App\Settings\SettingsRepository;
use App\Events\Setting\Saved;
use Illuminate\Support\Arr;
use Discuz\Qcloud\QcloudTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Ms\V20180408\Models\DescribeUserBaseInfoInstanceRequest;
use TencentCloud\Ms\V20180408\MsClient;
use function Clue\StreamFilter\fun;

class QcloudDaily
{
    use QcloudTrait;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var SettingsRepository
     */
    public $settings;

    /**
     * @param Request $request
     * @param SettingsRepository $settings
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function handle()
    {
        $tomorrow = date("Y-m-d",strtotime("+1 day"));
        $cache_time = strtotime($tomorrow) - time();
        $settings = Setting::query()->get()->pluck('value', 'key')->toArray();
        $isset_daily = app('cache')->get($settings['site_id']);
        if($isset_daily){
            return;
        }
        $qcloudSecretId = $settings['qcloud_secret_id'];
        $qcloudSecretKey = $settings['qcloud_secret_key'];
        if(empty($qcloudSecretId) || empty($qcloudSecretKey)){
            $uin = '';
        }else{
            try {
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
                $uin = $resp->UserUin;
            }catch (\Exception $e){
                $uin = '';
            }
        }
        $json = [
            'site_id' => $settings['site_id'] ?? '',
            'site_name' =>  !empty($settings['site_name']) ? $settings['site_name'] : '',
            'site_uin'  =>  $uin,
            'site_url'  =>  !empty($settings['site_url']) ? $settings['site_url'] : '',
            'site_on'   =>  empty($settings['site_close']) ? 1 : 0,
            'qcloud_close'  =>  $settings['qcloud_close'] ?? 0,
            'qcloud_secret_id'  =>  $qcloudSecretId,
            'cos_on'    =>  $settings['qcloud_cos'] ?? 0,
            'sms_on'    =>  $settings['qcloud_sms'] ?? 0,
            'vod_on'    =>  $settings['qcloud_vod'] ?? 0,
            'faceid_on' =>  $settings['qcloud_faceid'] ?? 0,
            'captcha_on'    =>  $settings['qcloud_captcha'] ?? 0,
            'ims_on'    =>  $settings['qcloud_cms_image'] ?? 0,
            'tms_on'    =>  $settings['qcloud_cms_text'] ?? 0,
            'qcloud_cms_image_init_time'    =>  $settings['qcloud_cms_image_init_time'] ?? null,
            'qcloud_cms_text_init_time'    =>  $settings['qcloud_cms_text_init_time'] ?? null,
            'qcloud_sms_init_time'    =>  $settings['qcloud_sms_init_time'] ?? null,
            'qcloud_faceid_init_time'    =>  $settings['qcloud_faceid_init_time'] ?? null,
            'qcloud_cos_init_time'    =>  $settings['qcloud_cos_init_time'] ?? null,
            'qcloud_vod_init_time'    =>  $settings['qcloud_vod_init_time'] ?? null,
            'qcloud_captcha_init_time'    =>  $settings['qcloud_captcha_init_time'] ?? null,

        ];
        try {
            $this->qcloudDaily($json)->wait();
            app('cache')->put($settings['site_id'] , 1, $cache_time);
        }catch (\Exception $e){

        }

    }

}
