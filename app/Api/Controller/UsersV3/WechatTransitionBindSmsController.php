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
use App\Commands\Users\RegisterPhoneUser;
use App\Common\AuthUtils;
use App\Common\ResponseCode;
use App\Events\Users\Logind;
use App\Events\Users\TransitionBind;
use App\Models\MobileCode;
use App\Models\SessionToken;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqLog;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Discuz\Contracts\Setting\SettingsRepository;

/**
 * 过渡时期，微信绑定手机
 * Class WechatTransitionBindSmsController
 * @package App\Api\Controller\UsersV3
 */
class WechatTransitionBindSmsController extends AuthBaseController
{
    public $bus;
    public $settings;
    public $events;
    public $connection;

    public function __construct(
        Dispatcher          $bus,
        SettingsRepository  $settings,
        Events              $events,
        ConnectionInterface $connection
    ){
        $this->bus          = $bus;
        $this->settings     = $settings;
        $this->events       = $events;
        $this->connection   = $connection;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $this->info('begin_wechat_transition_bind_sms_process');
        //过渡开关未打开
        if(!(bool)$this->settings->get('is_need_transition')) {
            $this->outPut(ResponseCode::TRANSITION_NOT_OPEN);
        }
        //未开启短信
        if(!(bool)$this->settings->get('qcloud_sms', 'qcloud')) {
            $this->outPut(ResponseCode::SMS_NOT_OPEN);
        }

        $this->connection->beginTransaction();
        try {
            $mobileCode = $this->getMobileCode('login');
    //        $mobileCode = MobileCode::query()->where('id',12)->first();
            $inviteCode = $this->inPut('inviteCode');
            $sessionToken = $this->inPut('sessionToken');
            $this->info('get_mobile_code', [
                'mobileCode'        =>  $mobileCode,
                'mobileCodeUser'    =>  $mobileCode->user
            ]);

            //register new user
            if (is_null($mobileCode->user)) {
                if (!(bool)$this->settings->get('register_close')) {
                    $this->connection->rollback();
                    $this->outPut(ResponseCode::REGISTER_CLOSE);
                }

                $exits = User::query()->where('mobile',$mobileCode->mobile)->lockForUpdate()->exists();
                if ($exits) {
                    $this->connection->rollback();
                    $this->outPut(ResponseCode::TRY_LOGIN_AGAIN);
                }

                $data['register_ip']    = ip($this->request->getServerParams());
                $data['register_port']  = Arr::get($this->request->getServerParams(), 'REMOTE_PORT', 0);
                $data['mobile']         = $mobileCode->mobile;
                $data['code']           = $inviteCode;
                $user = $this->bus->dispatch(
                    new RegisterPhoneUser($this->user, $data)
                );
                $mobileCode->setRelation('user', $user);

                $this->updateUserBindType($mobileCode->user,AuthUtils::PHONE);
            }

            //手机用户绑定微信操作
            $this->events->dispatch(new TransitionBind($mobileCode->user, ['sessionToken' => $sessionToken]));
            $this->info('updated_user', [
                'user'    =>  $mobileCode->user
            ]);

            //手机号登录需要填写扩展字段审核的场景
            if($mobileCode->user->status != User::STATUS_MOD){
                $this->events->dispatch(
                    new Logind($mobileCode->user)
                );
            }
            //login
            $params = [
                'username' => $mobileCode->user->username,
                'password' => ''
            ];
            GenJwtToken::setUid($mobileCode->user->id);
            $response = $this->bus->dispatch(
                new GenJwtToken($params)
            );
            $accessToken = json_decode($response->getBody(), true);
            $this->info('generate_accessToken',[
                'username'      =>  $mobileCode->user->username,
                'userId'        =>  $mobileCode->user->id,
                'accessToken'   =>  $accessToken,
            ]);
            $result = $this->camelData(collect($accessToken));
            $result = $this->addUserInfo($mobileCode->user, $result);
            $this->connection->commit();
            $this->outPut(ResponseCode::SUCCESS, '', $result);
        } catch (\Exception $e) {
            DzqLog::error('wechat_transition_bind_sms_api_error', [
                'mobile'        => $this->inPut('mobile'),
                'code'          => $this->inPut('code'),
                'sessionToken'  => $this->inPut('sessionToken'),
                'inviteCode'    => $this->inPut('inviteCode')
            ], $e->getMessage());
            $this->connection->rollback();
            $this->outPut(ResponseCode::INTERNAL_ERROR, '过渡阶段微信绑定手机号接口异常');
        }
    }
}
