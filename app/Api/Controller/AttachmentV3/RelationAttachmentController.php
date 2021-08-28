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

namespace App\Api\Controller\AttachmentV3;

use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\Attachment;
use App\Repositories\UserRepository;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Illuminate\Support\Str;

class RelationAttachmentController extends DzqController
{

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $user = $this->user;
        $data = [
            'typeId' => (int) $this->inPut('typeId'),
            'order' => (int) $this->inPut('order'),
            'type' => (int) $this->inPut('type'),
            'filePath' => $this->inPut('filePath'),
            'fileName' => $this->inPut('fileName'),
            'fileSize' => (int) $this->inPut('fileSize'),
            'fileWidth' => (int) $this->inPut('fileWidth'),
            'fileHeight' => (int) $this->inPut('fileHeight'),
            'fileType' => $this->inPut('fileType'),
            'requestId' => $this->inPut('requestId')
        ];

        $this->dzqValidate($data,[
                'type' => 'required|integer|in:0,1,2,3,4',
                'filePath' => 'required',
                'fileName' => 'required|max:200',
                'fileType' => 'required',
                'requestId' => 'required',
            ]
        );

        $fileName = explode('.',$data['fileName']);
        if (!isset($fileName[1])) {
            $this->outPut(ResponseCode::INVALID_PARAMETER,'fileNamec参数格式不正确');
        }

        $get = DzqCache::get(CacheKey::IMG_UPLOAD_TMP_DETECT);
        if (!isset($get[$user->id][$data['requestId']])) {
            $this->outPut(ResponseCode::INVALID_PARAMETER,'requestId参数无效');
        }

        $userCache = $get[$user->id][$data['requestId']];
        if ($userCache['createdAt'] < time()-1800) {
            $this->outPut(ResponseCode::INVALID_PARAMETER,'requestId参数过期');
        }

        $attachment = new Attachment();
        $attachment->uuid = Str::uuid();
        $attachment->user_id = $user->id;
        $attachment->type_id = $data['typeId'];
        $attachment->order = $data['order'];
        $attachment->type = $data['type'];
        $attachment->is_approved = Attachment::APPROVED;
        $attachment->attachment = Str::random(40) . $fileName[1];
        $attachment->file_path = $data['filePath'];
        $attachment->file_name = $data['fileName'];
        $attachment->file_size = $data['fileSize'];
        $attachment->file_width = $data['fileWidth'];
        $attachment->file_height = $data['fileHeight'];
        $attachment->file_type = $data['fileType'];
        $attachment->is_remote = Attachment::YES_REMOTE;
        $attachment->ip = ip($this->request->getServerParams());
        $attachment->save();

        $this->outPut(ResponseCode::SUCCESS,'',$attachment);
    }

    //替换缓存
    public function suffixClearCache($user)
    {
        $requestId = $this->inPut('requestId');
        $get = DzqCache::get(CacheKey::IMG_UPLOAD_TMP_DETECT);
        foreach ($get as $key=>$val) {
            unset($get[$key][$requestId]);
            foreach ($val as $k => $v) {
                if ($v['createdAt'] < time()-1800) {
                    unset($get[$key][$k]);
                }
            }
        }
        if (DzqCache::CACHE_TTL) {
            app('cache')->put(CacheKey::IMG_UPLOAD_TMP_DETECT, $get);
        }
    }

}
