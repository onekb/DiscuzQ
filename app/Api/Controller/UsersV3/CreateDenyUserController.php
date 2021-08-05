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
use App\Events\DenyUsers\Saved;
use App\Models\DenyUser;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Validation\Factory;

class CreateDenyUserController extends DzqController
{

    public $include = ['deny'];

    protected $validation;

    protected $events;

    public function __construct(Factory $validation, Dispatcher $events)
    {
        $this->validation = $validation;
        $this->events = $events;
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
        $actor = $this->user;

        $id = $this->inPut('id');

        if ($actor->id == $id) {
            return $this->outPut(ResponseCode::NET_ERROR, 'deny_self');
        }

        $denyUser = DenyUser::where('user_id', $actor->id)
            ->where('deny_user_id', $id)
            ->first();

        $denyUser = $denyUser ?? new DenyUser();
        $denyUser->user_id = $actor->id;
        $denyUser->deny_user_id = $id;

        $validation = $this->validation->make(
            $denyUser->getAttributes(),
            [
                'user_id' => 'required',
                'deny_user_id' => 'required'
            ]
        );
        $validation->failed();
        $denyUser->save();

         $this->events->dispatch(
            new Saved($denyUser, $actor)
        );

        $data = $this->camelData($denyUser);
        return $this->outPut(ResponseCode::SUCCESS, '',$data);

    }
}
