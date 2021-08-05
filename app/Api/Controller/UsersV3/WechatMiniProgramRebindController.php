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
use App\Models\UserWechat;
use App\Repositories\UserRepository;
use App\User\Bind;
use App\User\Bound;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Guest;
use Discuz\Base\DzqLog;
use Discuz\Contracts\Socialite\Factory;
use Discuz\Wechat\EasyWechatTrait;
use Exception;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Database\ConnectionInterface;

class WechatMiniProgramRebindController extends AuthBaseController
{
    use AssertPermissionTrait;
    use EasyWechatTrait;

    protected $socialite;
    protected $validation;
    protected $bind;
    protected $db;
    protected $bound;

    public function __construct(
        Factory             $socialite,
        ValidationFactory   $validation,
        Bind                $bind,
        ConnectionInterface $db,
        Bound               $bound
    ){
        $this->socialite    = $socialite;
        $this->validation   = $validation;
        $this->bind         = $bind;
        $this->db           = $db;
        $this->bound        = $bound;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        try {
            $param          = $this->getWechatMiniProgramParam();
            $sessionToken   = $this->inPut('sessionToken');// PC扫码使用
    //        $sessionToken   = 'GJt8B1AxRjhLHR1VGJ2ZpsW5mLa6aRP2';
            $token          = SessionToken::get($sessionToken);
            $actor          = !empty($token->user) ? $token->user : $this->user;
        } catch (Exception $e) {
            DzqLog::error('wechat_mini_program_rebind_api_error', [
                'sessionToken'  => $this->inPut('sessionToken')
            ], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '小程序参数换绑接口异常');
        }

        if (empty($actor)) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }

        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::UNAUTHORIZED,'访客无权限换绑小程序');
        }

        if (is_null($actor->wechat)) {
            $this->outPut(ResponseCode::PC_REBIND_ERROR,'用户需先绑定微信才可进行换绑');
        }

        // 绑定小程序
        $this->db->beginTransaction();
        try {
            $app = $this->miniProgram();
            $wechatUser = $this->getMiniWechatUser(
                $app,
                $param['jsCode'],
                $param['iv'],
                $param['encryptedData']
            );

            if (!$wechatUser || !$wechatUser->user) {
                if (!$wechatUser) {
                    $wechatUser = new UserWechat();
                }

                //删除用户原先绑定的微信信息
                UserWechat::query()->where('user_id', $actor->id)->delete();

                $wechatUser->user_id = $actor->id;
                // 先设置关系再save，为了同步微信头像
                $wechatUser->setRelation('user', $actor);
                $wechatUser->save();

                $this->db->commit();

                // PC扫码使用
                if ($sessionToken) {
                    $this->bound->rebindVoid($sessionToken, $wechatUser);
                }

                $this->outPut(ResponseCode::SUCCESS, '', []);
            } else {
                $this->db->rollBack();
                $this->outPut(ResponseCode::ACCOUNT_HAS_BEEN_BOUND, '微信号已绑定其他账户');
            }
        } catch (Exception $e) {
            DzqLog::error('wechat_mini_program_rebind_api_error', [
                'sessionToken'  => $this->inPut('sessionToken')
            ], $e->getMessage());
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR,'小程序换绑接口异常');
        }
    }
}
