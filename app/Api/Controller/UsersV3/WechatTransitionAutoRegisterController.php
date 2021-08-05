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


use App\Censor\Censor;
use App\Commands\Users\AutoRegisterUser;
use App\Commands\Users\GenJwtToken;
use App\Common\AuthUtils;
use App\Common\ResponseCode;
use App\Models\SessionToken;
use App\Models\User;
use App\Models\UserWechat;
use App\Notifications\Messages\Wechat\RegisterWechatMessage;
use App\Notifications\System;
use App\Validators\UserValidator;
use Discuz\Base\DzqLog;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as Events;
use App\Settings\SettingsRepository;
use Illuminate\Support\Str;
use Illuminate\Database\ConnectionInterface;
/**
 * 过渡阶段微信绑定新注册用户
 * Class WechatTransitionH5LoginController
 * @package App\Api\Controller\UsersV3
 */
class WechatTransitionAutoRegisterController extends AuthBaseController
{

    protected $bus;

    protected $settings;

    protected $events;

    protected $censor;

    protected $validator;

    protected $db;

    public function __construct(Dispatcher $bus, SettingsRepository $settings, Events $events, Censor $censor, UserValidator $validator, ConnectionInterface $db)
    {
        $this->bus = $bus;
        $this->settings = $settings;
        $this->events = $events;
        $this->censor = $censor;
        $this->validator = $validator;
        $this->db = $db;

    }
    public function main()
    {
        $this->info('begin_wechat_transition_auto_register_process');
        // 站点关闭注册
        if (!(bool)$this->settings->get('register_close')) {
            $this->outPut(ResponseCode::REGISTER_CLOSE);
        }
        if(!(bool)$this->settings->get('is_need_transition')) {
            $this->outPut(ResponseCode::TRANSITION_NOT_OPEN);
        }
        $type = intval($this->inPut('type'));
        //过度页开关打开需要把微信信息绑定至新用户，只在微信内有效
        $sessionToken = $this->inPut('sessionToken');
        if(! $sessionToken) {
            $this->outPut(ResponseCode::INVALID_PARAMETER,'sessionToken不能为空');
        }

        $this->db->beginTransaction();
        try {
            $token = SessionToken::get($sessionToken);
            $this->info('get_token_with_session_token', [
                'input'  => ['sessionToken' => $sessionToken],
                'output' => ['token' => $token]
            ]);
            if(! $token) {
                //授权信息过期，重新授权
                $this->outPut(ResponseCode::AUTH_INFO_HAD_EXPIRED);
            }

            //获取授权后微信用户信息
            $wxuser         = $token->payload;
            $inviteCode     = $this->inPut('inviteCode');//邀请码非必须存在

            /** @var UserWechat $wechatUser */
            $wechatUser = UserWechat::query()
                ->where('id', $wxuser['user_wechat_id'])
                ->lockForUpdate()
                ->first();
            $this->info('get_wxuser_with_wechat_id', [
                'wxuser' => $wxuser,
                'wechatUser'   => $wechatUser
            ]);
    //        $wechatUser = UserWechat::query()->where('id',63)->first();

            if(! $wechatUser) {
                $this->db->rollBack();
                //授权信息过期，重新授权
                $this->outPut(ResponseCode::AUTH_INFO_HAD_EXPIRED);
            }
            $data['code']               = $inviteCode;
            $data['username']           = Str::of($wechatUser->nickname)->substr(0, 15);
            $data['nickname']           = Str::of($wechatUser->nickname)->substr(0, 15);
            $data['register_reason']    = $type == 1 ? trans('user.register_by_wechat_miniprogram') : trans('user.register_by_wechat_h5');
            $data['bind_type']          = AuthUtils::WECHAT;
            $user = $this->bus->dispatch(
                new AutoRegisterUser(new User(), $data)
            );
            $wechatUser->user_id = $user->id;
            // 先设置关系，为了同步微信头像
            $wechatUser->setRelation('user', $user);

            $wechatUser->save();
            $this->info('updated_wechat_user_and_user', [
                'wechatUser'    =>  $wechatUser,
                'user'          =>  $user
            ]);

            // 判断是否开启了注册审核
    //        if (!(bool)$this->settings->get('register_validate')) {
    //            // Tag 发送通知 (在注册绑定微信后 发送注册微信通知)
    //            $user->setRelation('wechat', $wechatUser);
    //            $user->notify(new System(RegisterWechatMessage::class, $user, ['send_type' => 'wechat']));
    //        }
            // 创建 token
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
                'accessToken'   =>  $accessToken,
            ]);
            //删除sessionToken
            $token->delete();
            $this->db->commit();
            $result = $this->camelData(collect($accessToken));
            $result = $this->addUserInfo($wechatUser->user, $result);

            $this->outPut(ResponseCode::SUCCESS, '', $result);
        } catch (\Exception $e) {
            DzqLog::error('wechat_transition_auto_register_api_error', [
                'sessionToken'  => $this->inPut('sessionToken'),
                'type'          => $this->inPut('type'),
                'inviteCode'    => $this->inPut('inviteCode'),
            ], $e->getMessage());
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR,'微信过渡阶段自动注册用户接口异常');
        }

    }
}
