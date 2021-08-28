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
use App\Settings\SettingsRepository;
use Discuz\Auth\Exception\NotAuthenticatedException;
use Discuz\Base\DzqController;
use Discuz\Base\DzqLog;
use Discuz\Wechat\EasyWechatTrait;

/**
 * 微信小程序 - 小程序码
 *
 * @package App\Api\Controller\Wechat
 */
class WechatMiniProgramCodeController extends DzqController
{
    use EasyWechatTrait;

    protected $settings;

    /**
     * WechatMiniProgramCodeController constructor.
     */
    public function __construct(SettingsRepository $settingsRepository)
    {
        $this->settings = $settingsRepository;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN,'');
        }
        return true;
    }

    public function main()
    {
        $pathEncode = $this->inPut("path");
        $path = urldecode($pathEncode);
        $width = $this->inPut("width");
        $colorR = $this->inPut("r");
        $colorG = $this->inPut("g");
        $colorB = $this->inPut("b");
        if(empty($path)){
            $this->outPut(ResponseCode::INVALID_PARAMETER, '跳转小程序路由路径不能为空');
        }

        $paramData = [
            'path'=>$path,
            'width'=>$width,
            'r'=>$colorR,
            'g'=>$colorG,
            'b'=>$colorB,
        ];
        //入参日志记录
        app('log')->info("生成海报接口入参:{path:{$path},width:{$width},r:{$colorR},g:{$colorG},b:{$colorB}}");

        if(!(bool)$this->settings->get('miniprogram_app_id', 'wx_miniprogram')
            || !(bool)$this->settings->get('miniprogram_app_secret', 'wx_miniprogram')
            || !(bool)$this->settings->get('miniprogram_close', 'wx_miniprogram')
        ){
            $this->outPut(ResponseCode::CONFIG_MINIPROGRAM_AND_OPEN);
        }

        try {
            $app = $this->miniProgram();
            $response = $app->app_code->get($path, [
                'width' => $width,
                'line_color' => [
                    'r' => $colorR,
                    'g' => $colorG,
                    'b' => $colorB,
                ],
            ]);
            $response = $response->withoutHeader('Content-disposition');

            if(is_array($response) && isset($response['errcode']) && isset($response['errmsg'])) {
                //todo 日志记录
                $this->outPut(ResponseCode::MINI_PROGRAM_QR_CODE_ERROR);
            }
            //图片二进制转base64
            $data = [
                'base64Img' => 'data:image/png;base64,' . base64_encode($response->getBody()->getContents())
            ];
            return $this->outPut(ResponseCode::SUCCESS, '', $data);
        } catch (\Exception $e) {
            DzqLog::error('wechat_mini_program_code_api_error', $paramData, $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '生成小程序二维码接口异常');
        }
    }
}
