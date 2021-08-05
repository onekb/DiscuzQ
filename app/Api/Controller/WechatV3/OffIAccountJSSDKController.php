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

namespace App\Api\Controller\WechatV3;


use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use App\Exceptions\TranslatorException;
use Discuz\Wechat\EasyWechatTrait;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use EasyWeChat\Kernel\Exceptions\RuntimeException;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Psr\SimpleCache\InvalidArgumentException;

class OffIAccountJSSDKController extends DzqController
{

    use EasyWechatTrait;


    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @param Dispatcher $bus
     * @param UrlGenerator $url
     */
    public function __construct(Dispatcher $bus, UrlGenerator $url)
    {
        $this->bus = $bus;
        $this->url = $url;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     * @throws TranslatorException
     */
    public function main()
    {
        $url = $this->inPut('url');
        if (blank($url)) {
            $this->outPut(ResponseCode::WECHAT_INVALID_UNKNOWN_URL_EXCEPTION);
        }

        $app = $this->offiaccount();

        // js functions
        $build = [
            'updateAppMessageShareData',
            'updateTimelineShareData',
        ];

        $app->jssdk->setUrl($url);

        try {
            $result = $app->jssdk->buildConfig($build, true, false, false);
        } catch (InvalidConfigException $e) {
            $this->outPut(ResponseCode::WECHAT_INVALID_CONFIG_EXCEPTION);
        } catch (RuntimeException $e) {
            $this->outPut(ResponseCode::WECHAT_RUNTIME_EXCEPTION);
        } catch (InvalidArgumentException $e) {
            $this->outPut(ResponseCode::WECHAT_INVALID_ARGUMENT_EXCEPTION);
        }

        return $this->outPut(ResponseCode::SUCCESS,'', $result);
    }


}
