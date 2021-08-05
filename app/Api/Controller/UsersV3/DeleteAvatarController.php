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
use App\Repositories\UserRepository;
use App\User\AvatarUploader;
use Discuz\Base\DzqController;

class DeleteAvatarController extends DzqController
{
    protected $users;

    protected $uploader;

    public function __construct(UserRepository $users, AvatarUploader $uploader)
    {
        $this->users = $users;
        $this->uploader = $uploader;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $userRepo->canDeleteAvatar($this->user);
    }


    public function main()
    {
        $pid = $this->inPut('aid');

        $user = $this->users->findOrFail($pid);

        $this->uploader->remove($user);

        $user->save();

        return $this->outPut(ResponseCode::SUCCESS,'',[]);
    }
}
