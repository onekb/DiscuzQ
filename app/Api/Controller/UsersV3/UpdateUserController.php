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

use App\Commands\Users\UpdateUser;
use App\Common\ResponseCode;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Bus\Dispatcher;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class UpdateUserController extends DzqController
{

    protected $bus;
    protected $settings;

    public function __construct(Dispatcher $bus,SettingsRepository $settings)
    {
        $this->bus = $bus;
        $this->settings = $settings;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN,'');
        }

        $user = User::query()->where('id', $this->inPut('id'))->first();
        if (!$user) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }
        $isSelf = $this->user->id == $user->id;

        return $isSelf || $this->user->isAdmin();
    }

    public function main()
    {
        $id = $this->inPut('id');
        if(empty($id)){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'');
        }

        $data = $this->inPut('data');
        if(empty($data)){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'');
        }

        $requestData = [];
        if(!empty($data['username'])){
            $requestData['username'] = $data['username'];
        }
        if(!empty($data['password'])){
            $requestData['passWord'] = $data['password'];
        }
        if(!empty($data['newPassword'])){
            $requestData['newPassword'] = $data['newPassword'];
        }
        if(!empty($data['passwordConfirmation'])){
            $requestData['password_confirmation'] = $data['passwordConfirmation'];
        }
        if(!empty($data['payPassword'])){
            $requestData['payPassword'] = $data['payPassword'];
        }
        if(!empty($data['payPasswordConfirmation'])){
            $requestData['pay_password_confirmation'] = $data['payPasswordConfirmation'];
        }
        if(!empty($data['payPasswordToken'])){
            $requestData['pay_password_token'] = $data['payPasswordToken'];
        }

        if(!empty($data['mobile'])){
            $requestData['mobile'] = $data['mobile'];
        }

        if(!empty($data['refuseMessage'])){
            $requestData['refuse_message'] = $data['refuseMessage'];
        }
        if(!empty($data['signature'])){
            $requestData['signature'] = $data['signature'];
        }
        if(!empty($data['registerReason'])){
            $requestData['register_reason'] = $data['registerReason'];
        }
        if(!empty($data['groupId'])){
            $requestData['groupId'] = $data['groupId'];
        }

        $result = $this->bus->dispatch(
            new UpdateUser(
                $id,
                $requestData,
                $this->user
            )
        );
        $data = $this->camelData($result);
        return $this->outPut(ResponseCode::SUCCESS,'', $data);
    }
}
