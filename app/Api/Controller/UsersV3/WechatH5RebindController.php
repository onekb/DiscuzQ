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
use App\User\Bound;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Base\DzqLog;
use Discuz\Contracts\Socialite\Factory;
use Exception;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;

class WechatH5RebindController extends AuthBaseController
{
    use AssertPermissionTrait;
    protected $socialite;
    protected $validation;
    protected $db;
    protected $bound;

    public function __construct(
        Factory             $socialite,
        ValidationFactory   $validation,
        ConnectionInterface $db,
        Bound               $bound
    ){
        $this->socialite    = $socialite;
        $this->validation   = $validation;
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
            $wxuser         = $this->getWxuser();
            $sessionToken   = $this->inPut('sessionToken');
            $token          = SessionToken::get($sessionToken);
            $actor          = !empty($token->user) ? $token->user : $this->user;
        } catch (Exception $e) {
            DzqLog::error('wechat_h5_rebind_api_error', [], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, 'H5换绑获取wx用户接口异常');
        }

        if (empty($actor)) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }

        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::UNAUTHORIZED,'访客无权限换绑公众号');
        }

        if (is_null($actor->wechat)) {
            $this->outPut(ResponseCode::PC_REBIND_ERROR,'用户需先绑定微信才可进行换绑');
        }

        $this->db->beginTransaction();
        try {
            /** @var UserWechat $wechatUser */
            $wechatUser = UserWechat::query()
                ->where('mp_openid', $wxuser->getId())
                ->orWhere('unionid', Arr::get($wxuser->getRaw(), 'unionid'))
                ->lockForUpdate()
                ->first();

            if (!$wechatUser || !$wechatUser->user) {
                // 更新微信用户信息
                if (!$wechatUser) {
                    $wechatUser = new UserWechat();
                }

                //删除用户原先绑定的微信信息
                UserWechat::query()->where('user_id', $actor->id)->delete();

                $wechatUser->setRawAttributes($this->fixData($wxuser->getRaw(), $actor));

                //添加新的换绑的微信信息
                $wechatUser->user_id = $actor->id;
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
            DzqLog::error('wechat_h5_rebind_api_error', [
                'sessionToken' => $this->inPut('sessionToken')
            ], $e->getMessage());
            $this->db->rollBack();
            $this->outPut(ResponseCode::INTERNAL_ERROR,'H5换绑接口异常');
        }
    }
}
