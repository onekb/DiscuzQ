<?php
/**
 * Copyright (C) 2021 Tencent Cloud.
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

namespace App\Api\Controller\ThreadsV3;


use App\Commands\Thread\CreateThreadVideo;
use App\Common\ResponseCode;
use App\Models\Thread;
use App\Models\ThreadVideo;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Validation\Factory;


class CreateThreadVideoController extends DzqController
{
    protected $bus;
    protected $validation;


    public $providers = [
    ];


    public function __construct(Dispatcher $bus, Factory $validation)
    {
        $this->bus = $bus;
        $this->validation = $validation;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $user = $this->user;
        if ($this->user->isGuest()) {
            throw new PermissionDeniedException('没有上传视频权限');
        }
        $type = $this->inPut('type');
        $type = ((int)$type) ?: ThreadVideo::TYPE_OF_VIDEO;
        if($type == 1){
            if (!$userRepo->canInsertAudioToThread($user)) {
                throw new PermissionDeniedException('没有发帖插入音频权限');
            }
        }else{
            if (!$userRepo->canInsertVideoToThread($user)) {
                throw new PermissionDeniedException('没有发帖插入视频权限');
            }
        }
        return true;
    }


    public function main()
    {
        $fileId = $this->inPut('fileId');
        if(empty($fileId)){
            return $this->outPut(ResponseCode::INVALID_PARAMETER);
        }
        $type = $this->inPut('type');
        $type = (int)$type ? (int)$type: ThreadVideo::TYPE_OF_VIDEO;

        $media_url = $this->inPut('mediaUrl');

        //驼峰改下划线，适应以前的方法
        $data['attributes']['file_id'] = $fileId;
        $data['attributes']['media_url'] = $media_url;
        $result = $this->bus->dispatch(
            new CreateThreadVideo(
                $this->user,
                new Thread,
                $type,
                $data
            )
        );
        $result = $this->camelData($result);
        return $this->outPut(ResponseCode::SUCCESS,'', $result);

    }


}
