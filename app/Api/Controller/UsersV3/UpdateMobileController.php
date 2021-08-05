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
use App\Common\AuthUtils;
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Bus\Dispatcher;
use Discuz\Base\DzqCache;


class UpdateMobileController extends AuthBaseController
{
    protected $bus;

    public function __construct(
        Dispatcher          $bus
    ){
        $this->bus      = $bus;
    }

    public function prefixClearCache($user)
    {
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_USERS, $user->id);
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $mobileCode     = $this->getMobileCode('update');
        $actor          = $this->user;
        if ($actor->exists) {
            $userSelf = User::query()->where('id',$actor->id)->where('mobile',$mobileCode->mobile)->first();
            if($userSelf){
                $this->outPut(ResponseCode::INVALID_PARAMETER,"当前已是该手机号,无需修改");
            }
            $userRecord = User::query()->where('mobile',$mobileCode->mobile)->first();
            if($userRecord){
                $this->outPut(ResponseCode::MOBILE_IS_ALREADY_BIND);
            }
            $actor->changeMobile($mobileCode->mobile);
            $actor->save();
            $this->updateUserBindType($actor, AuthUtils::PHONE);
            $this->outPut(ResponseCode::SUCCESS, '', []);
        }
        $this->outPut(ResponseCode::INVALID_PARAMETER);
    }
}
