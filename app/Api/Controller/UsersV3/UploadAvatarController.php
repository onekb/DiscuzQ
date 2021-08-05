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
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class UploadAvatarController extends DzqController
{
    public function prefixClearCache($user)
    {
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_USERS, $user->id);
    }

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
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $uploadFile = $this->request->getUploadedFiles();
        if(empty($uploadFile)){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'');
        }
        $file = $uploadFile['avatar'];
        $actor = $this->user;
        $id = $actor->id;

        if(empty($id) || empty($file)){
             $this->outPut(ResponseCode::INVALID_PARAMETER,'');
        }

        $actor = $this->user;
        $result = $this->bus->dispatch(
            new UploadAvatar($id, $file, $actor)
        );
        $originalAvatar = $result->getOriginalAvatarPath();
        $result = [
            'id' => $result->id,
            'username' => $result->username,
            'avatarUrl' => $result->avatar,
            'originalAvatarUrl'=>$originalAvatar,
            'updatedAt' => optional($result->updated_at)->format('Y-m-d H:i:s'),
            'createdAt' => optional($result->created_at)->format('Y-m-d H:i:s'),
        ];

        return $this->outPut(ResponseCode::SUCCESS,'', $result);
    }
}
