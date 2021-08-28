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

use App\Censor\Censor;
use App\Commands\Attachment\AttachmentUploader;
use App\Common\ResponseCode;
use App\Models\Attachment;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Http\UploadedFile;

class RelationAttachmentController extends DzqController
{
    use AttachmentTrait;

    protected $censor;

    protected $image;

    protected $uploader;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $type = (int) $this->inPut('type'); //0 附件 1图片 2视频 3音频 4消息图片
        $this->checkUploadAttachmentPermissions($type, $this->user, $userRepo);
        return true;
    }

    public function __construct(Censor $censor, ImageManager $image, AttachmentUploader $uploader)
    {
        $this->censor   = $censor;
        $this->image    = $image;
        $this->uploader = $uploader;
    }

    public function main()
    {
        $data = [
            'cosUrl' => $this->inPut('cosUrl'),
            'type' => (int)$this->inPut('type'),
            'fileName' => $this->inPut('fileName')
        ];

        $this->dzqValidate($data, [
                'cosUrl' => 'required',
                'type' => 'required|integer|in:0,1,2,3,4',
                'fileName' => 'required|max:200'
            ]
        );

        $cosUrl = $data['cosUrl'];
        if (in_array($data['type'], [Attachment::TYPE_OF_IMAGE, Attachment::TYPE_OF_DIALOG_MESSAGE])) {
            $fileInfo = $this->getImageInfo($cosUrl, $this->censor);
        } else {
            $fileInfo = $this->getDocumentInfo($cosUrl);
        }

        $this->checkAttachmentExt($data['type'], $fileInfo['ext']);
        $this->checkAttachmentSize($fileInfo['fileSize']);
        $mimeType = $this->getAttachmentMimeType($cosUrl);

        // 模糊图处理
        if ($data['type'] == Attachment::TYPE_OF_IMAGE) {
            $tmpFile = tempnam(storage_path('/tmp'), 'attachment');
            $tmpFileWithExt = $tmpFile . $fileInfo['ext'];
            @file_put_contents($tmpFileWithExt, $this->getFileContents($cosUrl));
            $blurImageFile = new UploadedFile(
                $tmpFileWithExt,
                $fileInfo['attachmentName'],
                $mimeType,
                0,
                true
            );
            // 帖子图片自适应旋转
            if(strtolower($fileInfo['ext']) != 'gif' && extension_loaded('exif')) {
                $this->image->make($tmpFileWithExt)->orientate()->save();
            }

            $this->uploader->put($data['type'], $blurImageFile, $fileInfo['attachmentName'], $fileInfo['filePath']);
            @unlink($tmpFile);
            @unlink($tmpFileWithExt);
        }

        $attachment = new Attachment();
        $attachment->uuid = Str::uuid();
        $attachment->user_id = $this->user->id;
        $attachment->type = $data['type'];
        $attachment->is_approved = Attachment::APPROVED;
        $attachment->attachment = $fileInfo['attachmentName'];
        $attachment->file_path = $fileInfo['filePath'];
        $attachment->file_name = $data['fileName'];
        $attachment->file_size = $fileInfo['fileSize'];
        $attachment->file_width = $fileInfo['width'];
        $attachment->file_height = $fileInfo['height'];
        $attachment->file_type = $mimeType;
        $attachment->is_remote = Attachment::YES_REMOTE;
        $attachment->ip = ip($this->request->getServerParams());
        $attachment->save();
        $attachment->url = $cosUrl;
        $attachment->thumbUrl = $fileInfo['thumbUrl'] ?: '';

        $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($attachment));
    }
}
