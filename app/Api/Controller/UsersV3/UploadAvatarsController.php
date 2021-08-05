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

use App\Commands\Users\UploadAvatar;
use App\Common\DzqCache;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class UploadAvatarsController extends DzqController
{
    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $userRepo->canCreateAvatar($this->user);
    }

    public function main()
    {
        $id =   $this->inPut('aid');
        $uploadFile = $this->request->getUploadedFiles();

        $file = $uploadFile['avatar'];
        if(empty($id) || empty($file)){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'');
        }
        $actor = $this->user;

        $result = $this->bus->dispatch(
            new UploadAvatar($id, $file, $actor)
        );
        $result = [
            'id' => $result->id,
            'username' => $result->username,
            'avatarUrl' => $result->avatar,
        ];

        return $this->outPut(ResponseCode::SUCCESS,'', $result);
    }

}
