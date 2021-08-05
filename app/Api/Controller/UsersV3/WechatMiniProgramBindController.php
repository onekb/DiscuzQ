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

use App\Commands\Users\GenJwtToken;
use App\Common\AuthUtils;
use App\Common\ResponseCode;
use App\Models\SessionToken;
use App\Models\User;
use App\Models\UserWechat;
use App\Repositories\UserRepository;
use App\User\Bind;
use App\User\Bound;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Guest;
use Discuz\Base\DzqLog;
use Discuz\Wechat\EasyWechatTrait;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Database\ConnectionInterface;

class WechatMiniProgramBindController extends AuthBaseController
{
    use AssertPermissionTrait;
    use EasyWechatTrait;

    protected $validation;
    protected $bind;
    protected $db;
    protected $bound;
    protected $bus;

    public function __construct(
        ValidationFactory   $validation,
        Bind                $bind,
        ConnectionInterface $db,
        Bound               $bound,
        Dispatcher          $bus
    ){
        $this->validation   = $validation;
        $this->bind         = $bind;
        $this->db           = $db;
        $this->bound        = $bound;
        $this->bus          = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $this->info('begin_mini_program_bind_process');
        try {
            $param          = $this->getWechatMiniProgramParam();
            $sessionToken   = $this->inPut('sessionToken');// PC扫码使用;
            $token          = SessionToken::get($sessionToken);
            $type           = $this->inPut('type');//用于区分sessionToken来源于pc还是h5
            $actor          = !empty($token->user) ? $token->user : $this->user;
        } catch (Exception $e) {
            DzqLog::error('wechat_mini_program_bind_api_error', [
                'sessionToken'  => $this->inPut('sessionToken'),
                'type'          => $this->inPut('type')
            ], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '小程序绑定参数获取接口异常');
        }
        $this->info('get_token_with_session_token', [
            'input'      => [
                'sessionToken' => $sessionToken
            ],
            'output'      => [
                'token'    => $token,
                'user'     => $actor
            ]
        ]);

        if (empty($actor)) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }

        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::UNAUTHORIZED,'访客无权限绑定小程序');
        }

        // 绑定小程序
        $this->db->beginTransaction();
        try {
            $app = $this->miniProgram();
            $wechatUser = $this->getMiniWechatUser(
                $param['jsCode'],
                $param['iv'],
                $param['encryptedData']
            );

            if (!$wechatUser || !$wechatUser->user) {
                if (!$wechatUser) {
                    $wechatUser = new UserWechat();
                    $this->info('new_user_wechat', ['wechatUser' =>  $wechatUser]);
                }

    //            $wechatUser->setRawAttributes($this->fixData($wxuser->getRaw(), $actor));
                // 登陆用户且没有绑定||换绑微信 添加微信绑定关系
                $wechatUser->user_id = $actor->id;
                $wechatUser->setRelation('user', $actor);
                $wechatUser->save();
                $this->updateUserBindType($actor,AuthUtils::WECHAT);
                $this->db->commit();
                $this->info('updated_wechat_user_and_user', [
                    'wechatUser'    =>  $wechatUser,
                    'user'          =>  $actor
                ]);

                // PC扫码使用
                if (!empty($sessionToken) && $type == 'pc') {
                    $accessToken = $this->getAccessToken($wechatUser->user);
                    $wechatUser = [
                        'nickname'      =>  $wechatUser['nickname'],
                        'headimgurl'    =>  $wechatUser['headimgurl']
                    ];
                    $this->bound->bindVoid($sessionToken, $wechatUser, $accessToken);
                    $this->info('pc_scan_qr_code_bind', [
                        'sessionToken'  =>  $sessionToken,
                        'wechatUser'    =>  $wechatUser,
                        'accessToken'   =>  $accessToken
                    ]);
//                    $accessToken = $this->getAccessToken($wechatUser->user);
//                    $this->bound->pcLogin($sessionToken, (array)$accessToken, ['user_id' => $wechatUser->user->id]);
                }

                //用于用户名登录绑定微信使用
                if (!empty($token->user) && $type == 'h5') {
                    if (empty($actor->username)) {
                        $this->outPut(ResponseCode::USERNAME_NOT_NULL);
                    }
                    //token生成
                    $accessToken = $this->getAccessToken($actor);
                    $result = $this->camelData(collect($accessToken));
                    $result = $this->addUserInfo($actor, $result);

                    $this->outPut(ResponseCode::SUCCESS, '', $result);
                }

                $this->outPut(ResponseCode::SUCCESS, '', []);

            } else {
                $this->db->rollBack();
                $this->outPut(ResponseCode::ACCOUNT_HAS_BEEN_BOUND, '微信号已绑定其他账户');
            }
        } catch (Exception $e) {
            DzqLog::error('wechat_mini_program_bind_api_error', [
                'sessionToken'  => $this->inPut('sessionToken'),
                'type'          => $this->inPut('type')
            ], $e->getMessage());
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR,'小程序绑定接口异常');
        }
    }
}
