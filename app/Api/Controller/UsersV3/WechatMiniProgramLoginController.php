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

use App\Commands\Users\AutoRegisterUser;
use App\Commands\Users\GenJwtToken;
use App\Common\AuthUtils;
use App\Common\ResponseCode;
use App\Events\Users\Logind;
use App\Exceptions\NoUserException;
use App\Models\SessionToken;
use App\Models\User;
use App\Models\UserWechat;
use App\Notifications\Messages\Wechat\RegisterWechatMessage;
use App\Notifications\System;
use App\Repositories\UserRepository;
use App\Settings\SettingsRepository;
use App\User\Bind;
use App\User\Bound;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Guest;
use Discuz\Base\DzqLog;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Str;

class WechatMiniProgramLoginController extends AuthBaseController
{
    use AssertPermissionTrait;


    protected $bus;
    protected $validation;
    protected $events;
    protected $settings;
    protected $bind;
    protected $bound;
    protected $db;
    protected $miniParam;
    protected $miniUser;
    protected $app;

    public function __construct(
        Dispatcher          $bus,
        ValidationFactory   $validation,
        Events              $events,
        SettingsRepository  $settings,
        Bind                $bind,
        Bound               $bound,
        ConnectionInterface $db
    ){
        $this->bus          = $bus;
        $this->validation   = $validation;
        $this->events       = $events;
        $this->settings     = $settings;
        $this->bind         = $bind;
        $this->bound        = $bound;
        $this->db           = $db;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $this->info('begin_wechat_miniProgram_login_process');
        try {
            $this->miniParam    = $this->getWechatMiniProgramParam();
            $sessionToken       = $this->inPut('sessionToken');
            $user               = !$this->user->isGuest() ? $this->user : new Guest();
            $this->miniUser     = $user;
            $inviteCode         = $this->inPut('inviteCode');
        } catch (Exception $e) {
            DzqLog::error('wechat_mini_program_get_param_error', [
                'inviteCode'    => $this->inPut('inviteCode'),
                'sessionToken'  => $this->inPut('sessionToken')
            ], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '小程序参数获取接口异常');
        }
        //过渡开关打开
        if((bool)$this->settings->get('is_need_transition') && empty($sessionToken)) {
            $this->transitionLoginLogicVoid();
        }

        // 绑定小程序
        $this->db->beginTransaction();
        try {
            $wechatUser = $this->getMiniWechatUser(
                $this->miniParam['jsCode'],
                $this->miniParam['iv'],
                $this->miniParam['encryptedData'],
                $user
            );
            $this->info('get_user_wechat',['wechatUser' => $wechatUser]);
            if (!$wechatUser || !$wechatUser->user) {
                // 更新微信用户信息
                if (!$wechatUser) {
                    $wechatUser = new UserWechat();
                    $this->info('new_user_wechat', ['wechatUser' =>  $wechatUser]);
                }
    //            $wechatUser->setRawAttributes($this->fixData($wxuser->getRaw(), $actor));

                // 自动注册
                if ($user->isGuest()) {
                    // 站点关闭注册
                    if (!(bool)$this->settings->get('register_close')) {
                        $this->db->rollBack();
                        $this->outPut(ResponseCode::REGISTER_CLOSE);
                    }

                    $data['code']               = $inviteCode;
                    $data['username']           = Str::of($wechatUser->nickname)->substr(0, 15);
                    $data['nickname']           = Str::of($wechatUser->nickname)->substr(0, 15);
                    $data['register_reason']    = trans('user.register_by_wechat_miniprogram');
                    $user = $this->bus->dispatch(
                        new AutoRegisterUser($this->user, $data)
                    );
                    $wechatUser->user_id = $user->id;
                    // 先设置关系，为了同步微信头像
                    $wechatUser->setRelation('user', $user);
                    $wechatUser->save();

                    $this->updateUserBindType($user,AuthUtils::WECHAT);

                    $this->info('auto_registered_and_updated', [
                        'input'      => [
                            'data'         => $data
                        ],
                        'output'      => [
                            'user'         => $user,
                            'wechatUser'   => $wechatUser
                        ]
                    ]);

                    // 判断是否开启了注册审核
    //                if (!(bool)$this->settings->get('register_validate')) {
    //                    // Tag 发送通知 (在注册绑定微信后 发送注册微信通知)
    //                    $user->setRelation('wechat', $wechatUser);
    //                    $user->notify(new System(RegisterWechatMessage::class, $user, ['send_type' => 'wechat']));
    //                }
                } else {
                    if (!$user->isGuest() && is_null($user->wechat)) {
                        // 登陆用户且没有绑定||换绑微信 添加微信绑定关系
                        $wechatUser->user_id = $user->id;
                        $wechatUser->setRelation('user', $user);
                        $wechatUser->save();

                        $this->updateUserBindType($user,AuthUtils::WECHAT);
                        $this->info('bound_wechat_to_user', [
                            'user'         =>  $user,
                            'wechatUser'    =>  $wechatUser
                        ]);
                    }
                }
            } else {
                // 登陆用户和微信绑定不同时，微信已绑定用户，抛出异常
                if (!$user->isGuest() && $user->id != $wechatUser->user_id) {
                    $this->info('wechat_user_has_been_bound', [
                        'user'         =>  $user,
                        'wechatUser'    =>  $wechatUser
                    ]);
                    $this->db->rollBack();
                    $this->outPut(ResponseCode::ACCOUNT_HAS_BEEN_BOUND, '登录用户与微信所绑定用户不一致');
                }

                // 登陆用户和微信绑定相同，更新微信信息
    //            $wechatUser->setRawAttributes($this->fixData($wxuser->getRaw(), $wechatUser->user));
                $wechatUser->save();
                $this->info('updated_wechat_user', ['wechatUser'    =>  $wechatUser]);
            }

            if (empty($wechatUser) || empty($wechatUser->user)) {
                $this->db->rollBack();
                DzqLog::error('wechat_user_is_not_null', ['wechatUser' => $wechatUser]);
                $this->outPut(ResponseCode::INVALID_PARAMETER);
            }
            $this->db->commit();
            //创建 token
            $params = [
                'username' => $wechatUser->user->username,
                'password' => ''
            ];
            GenJwtToken::setUid($wechatUser->user->id);
            $response = $this->bus->dispatch(
                new GenJwtToken($params)
            );

            //小程序无感登录，待审核状态
            if ($response->getStatusCode() === 200) {
                if($wechatUser->user->status!=User::STATUS_MOD){
                    $this->info('begin_logind',['user'  =>  $wechatUser->user]);
                    $this->events->dispatch(new Logind($wechatUser->user));
                }
            }

            $accessToken = json_decode($response->getBody());

            $this->info('generate_accessToken',[
                'username'      =>  $wechatUser->user->username,
                'userId'        =>  $wechatUser->user->id,
                'accessToken'   =>  $accessToken,
            ]);

            // bound
            if ($sessionToken) {
                $this->info('begin_pc_qrcode', ['sessionToken' => $sessionToken]);
                $accessToken = $this->bound->pcLogin($sessionToken, (array)$accessToken, ['user_id' => $wechatUser->user->id]);

                $this->updateUserBindType($wechatUser->user,AuthUtils::WECHAT);
                $this->info('end_pc_qrcode',[
                    'sessionToken'  =>  $sessionToken,
                    'accessToken'   =>  $accessToken,
                    'user'          =>  $wechatUser->user
                ]);
            }

            $result = $this->camelData(collect($accessToken));
            $result = $this->addUserInfo($wechatUser->user, $result);
            $this->outPut(ResponseCode::SUCCESS, '', $result);
        } catch (Exception $e) {
            DzqLog::error('wechat_mini_program_login_api_error', [], $e->getMessage());
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR,'小程序登录接口异常');
        }
    }

    /**
     * 过渡阶段微信登录，过渡开关打开走此流程
     */
    private function transitionLoginLogicVoid()
    {
        $this->info('begin_transition_process',['miniUser' => $this->miniUser]);
        $this->db->beginTransaction();
        try {
            /** @var UserWechat $wechatUser */
            $wechatUser = $this->getMiniWechatUser(
                $this->miniParam['jsCode'],
                $this->miniParam['iv'],
                $this->miniParam['encryptedData'],
                $this->miniUser
            );
            $this->info('get_user_wechat',['wechatUser' => $wechatUser]);
            // 微信信息不存在
            if(! $wechatUser) {
                $wechatUser = new UserWechat();
                $this->info('new_user_wechat', ['wechatUser' =>  $wechatUser]);
            }
            $userWechatId = $wechatUser ? $wechatUser->id : 0;
            if(is_null($wechatUser->user)) {
                // 站点关闭注册
                if (!(bool)$this->settings->get('register_close')) {
                    $this->db->rollBack();
                    $this->outPut(ResponseCode::REGISTER_CLOSE);
                }
    //            $wechatUser->setRawAttributes($this->fixData($wxuser->getRaw(), new User()));
                $wechatUser->save();//微信信息写入user_wechats
                $userWechatId = $wechatUser->id ? $wechatUser->id : $userWechatId;
                //生成sessionToken,并把user_wechats 信息写入session_token
                $token = SessionToken::generate(SessionToken::WECHAT_TRANSITION_LOGIN, ['user_wechat_id' => $userWechatId], null, 1800);
                $token->save();
                $sessionToken = $token->token;
                $this->info('generate_token', [
                    'input'      => [
                        'scope'         => SessionToken::WECHAT_TRANSITION_LOGIN,
                        'user_wechat'   => $wechatUser
                    ],
                    'output'      => [
                        'token'         => $token
                    ]
                ]);
                $this->db->commit();
                //把token返回用户绑定用户使用
                $this->outPut(ResponseCode::NEED_BIND_USER_OR_CREATE_USER, '', ['sessionToken' => $sessionToken, 'nickname' => $wechatUser->nickname]);
            }

            // 登陆用户和微信绑定相同，更新微信信息
    //        $wechatUser->setRawAttributes($this->fixData($wxuser->getRaw(), $wechatUser->user));
            $wechatUser->save();
            $this->info('updated_wechat_user',['wechatUser' =>  $wechatUser]);
            $this->db->commit();

            // 生成token
            $params = [
                'username' => $wechatUser->user->username,
                'password' => ''
            ];
            GenJwtToken::setUid($wechatUser->user->id);
            $response = $this->bus->dispatch(
                new GenJwtToken($params)
            );
            $accessToken = json_decode($response->getBody());

            $this->info('generate_accessToken',[
                'username'      =>  $wechatUser->user->username,
                'userId'        =>  $wechatUser->user->id,
                'accessToken'   =>  $accessToken
            ]);

            $result = $this->camelData(collect($accessToken));
            $result = $this->addUserInfo($wechatUser->user, $result);
            $this->outPut(ResponseCode::SUCCESS, '', $result);
        } catch (Exception $e) {
            DzqLog::error('wechat_mini_program_login_api_error', [], $e->getMessage());
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR,'小程序登录接口异常');
        }
    }
}
