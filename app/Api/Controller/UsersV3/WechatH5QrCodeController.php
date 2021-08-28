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
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Discuz\Base\DzqLog;
use Endroid\QrCode\QrCode;
use Illuminate\Contracts\Routing\UrlGenerator;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Wechat\EasyWechatTrait;

class WechatH5QrCodeController extends AuthBaseController
{

    use EasyWechatTrait;
    use AssertPermissionTrait;


    public $optionalInclude = [];

    /**
     * @var UrlGenerator
     */
    protected $url;

    public $paramData = [];

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

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        try {
            $this->paramData = [
                'type'          =>  $this->inPut('type'),
                'redirectUri'   =>  urldecode($this->inPut('redirectUri')),
                'sessionToken'  =>  $this->inPut('sessionToken'),
                'process'       =>  $this->inPut('process'),
                'userId'        =>  $this->user->id
            ];

            if(! in_array($this->paramData['type'], self::$qrcodeType)) {
                $this->outPut(ResponseCode::GEN_QRCODE_TYPE_ERROR);
            }
            //分离出参数
            $conData = $this->parseUrlQuery($this->paramData['redirectUri']);
            //回调页面url
            $redirectUri = $conData['url'];
            //参数
            $query = $conData['params'];
            //手机浏览器绑定则由前端传session_token
            $sessionToken = $this->paramData['sessionToken'];
            if($this->paramData['type'] == 'mobile_browser_bind' && ! $sessionToken) {
                // 非登录流程下绑定微信用户
                if ($this->paramData['process'] == 'bind') {
                    if (! $this->user->isGuest()) {
                        $accessToken = $this->getAccessToken($this->user);
                        $token = SessionToken::generate(
                            SessionToken::WECHAT_OFFIACCOUNT_QRCODE_BIND,
                            $accessToken,
                            $this->user->id
                        );
                        $token->save();
                        $sessionToken = $token->token;
                    } else {
                        $this->outPut(ResponseCode::INVALID_PARAMETER, '用户不存在', ['id' => $this->user->id]);
                    }
                } else {
                    $this->outPut(ResponseCode::GEN_QRCODE_TYPE_ERROR);
                }
            }
            if($this->paramData['type'] != 'mobile_browser_bind') {
                //跳转路由选择
                $actor = $this->user;
                if ($this->paramData['type'] == 'pc_bind') {
                    $userId = $this->getCookie('dzq_user_id');
                    $actor = User::query()->where('id', (int)$userId)->first();
                    if (empty($actor)) {
                        $this->outPut(ResponseCode::JUMP_TO_LOGIN);
                    }
                }

                if($actor && $actor->id) {
                    $token = SessionToken::generate(self::$qrcodeTypeAndIdentifierMap[$this->paramData['type']], null, $actor->id);
                } else {
                    $token = SessionToken::generate(self::$qrcodeTypeAndIdentifierMap[$this->paramData['type']]);
                }
                // create token
                $token->save();

                $sessionToken = $token->token;
            }
            $query = array_merge($query, ['sessionToken' => $sessionToken]);
            $locationUrl = $this->url->action('/apiv3/users/wechat/h5.oauth?redirect='.$redirectUri, $query);
            $locationUrlArr = explode('redirect=', $locationUrl);
            $locationUrl = $locationUrlArr[0].'redirect='.urlencode($locationUrlArr[1]);
            //去掉无参数时最后一个是 ? 的字符
            $locationUrl = rtrim($locationUrl, "?");

            $qrCode = new QrCode($locationUrl);

            $binary = $qrCode->writeString();

            $baseImg = 'data:image/png;base64,' . base64_encode($binary);

            $data = [
                'sessionToken' => $sessionToken,
                'base64Img' => $baseImg,
            ];
            if($this->paramData['type']=='mobile_browser_login') {
                unset($data['sessionToken']);
            }

            $this->outPut(ResponseCode::SUCCESS, '', $data);
        } catch (\Exception $e) {
            DzqLog::error('wechat_h5_qr_code_api_error', $this->paramData, $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, 'h5二维码生成接口异常');
        }

    }

    /**
     *
     * 从url 中分离出uri与参数
     * @param $url
     * @return mixed
     */
    protected function parseUrlQuery($url)
    {
        $urlParse = explode('?', $url);
        $data['url'] = $urlParse[0];
        $data['params'] = [];
        if(isset($urlParse[1]) && !empty($urlParse[1])) {
            $queryParts = explode('&', $urlParse[1]);
            $params = array();
            foreach ($queryParts as $param) {
                $item = explode('=', $param);
                $params[$item[0]] = $item[1];
            }
            $data['params'] = $params;
        }
        return $data;
    }

}
