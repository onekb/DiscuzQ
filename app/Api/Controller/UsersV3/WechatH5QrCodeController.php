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
use Discuz\Base\DzqController;
use Endroid\QrCode\QrCode;
use Illuminate\Contracts\Routing\UrlGenerator;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Wechat\EasyWechatTrait;

class WechatH5QrCodeController extends DzqController
{

    use EasyWechatTrait;
    use AssertPermissionTrait;


    public $optionalInclude = [];

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * 二维码生成类型
     * @var string[]
     */
    static $qrcodeType = [
        'pc_login',
        'pc_bind',
        'mobile_browser_login',
        'mobile_browser_bind'
    ];

    /**
     * 二维码生成类型与跳转路由的映射
     * @var string[]
     */
    //todo 对接前端时更换路由
    static $qrcodeTypeAndRouteMap = [
        'pc_login'              => '/pages/user/pc-login',
        'pc_bind'               => '/pages/user/pc-relation',
        'mobile_browser_login'  => '/pages/user/pc-login',
        'mobile_browser_bind'   => '/pages/user/pc-relation'
    ];

    /**
     * 二维码生成类型与token标识映射
     * @var array
     */
    static $qrcodeTypeAndIdentifierMap = [
        'pc_login'              => SessionToken::WECHAT_PC_LOGIN,
        'pc_bind'               => SessionToken::WECHAT_PC_BIND,
        'mobile_browser_login'  => SessionToken::WECHAT_MOBILE_LOGIN,
        'mobile_browser_bind'   => SessionToken::WECHAT_MOBILE_BIND
    ];

    /**
     * @param UrlGenerator $url
     */
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function main()
    {
        $type = $this->inPut('type');
        if(! in_array($type, self::$qrcodeType)) {
            $this->outPut(ResponseCode::GEN_QRCODE_TYPE_ERROR, ResponseCode::$codeMap[ResponseCode::GEN_QRCODE_TYPE_ERROR]);
        }

        //跳转路由选择
        $route = self::$qrcodeTypeAndRouteMap[$type];
        $actor = $this->user;
        if($actor && $actor->id) {
            $token = SessionToken::generate(self::$qrcodeTypeAndIdentifierMap[$type], null, $actor->id);
        } else {
            $token = SessionToken::generate(self::$qrcodeTypeAndIdentifierMap[$type]);
        }
        // create token
        $token->save();

        $locationUrl = $this->url->action($route, ['session_token' => $token->token]);

        $qrCode = new QrCode($locationUrl);

        $binary = $qrCode->writeString();

        $baseImg = 'data:image/png;base64,' . base64_encode($binary);

        $data = [
            'session_token' => $token->token,
            'base64_img' => $baseImg,
        ];

        $this->outPut(ResponseCode::SUCCESS, '', $data);
    }

}
