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
use League\Flysystem\Util;
use Illuminate\Http\UploadedFile;

class RelationAttachmentController extends DzqController
{
    use AttachmentTrait;

    protected $censor;

    protected $settings;

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
        $header_array = get_headers($cosUrl, true, stream_context_set_default(['ssl'=>['verify_peer'=>false, 'verify_peer_name'=>false]]));
        $fileSize = $header_array['Content-Length'];
        $fileData = parse_url($cosUrl);
        $fileData = pathinfo($fileData['path']);

        $this->checkAttachmentSize($fileSize);
        $ext = $this->checkAttachmentExt($data['type'], $fileData['basename']);

        set_time_limit(0);
        $ext = $ext ? ".$ext" : '';
        $tmpFile = tempnam(storage_path('/tmp'), 'attachment');
        $tmpFileWithExt = $tmpFile . $ext;
        $putResult = @file_put_contents($tmpFileWithExt, $cosUrl);
        if (!$putResult) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '未获取到文件信息！');
        }
        if (in_array($data['type'], [Attachment::TYPE_OF_IMAGE, Attachment::TYPE_OF_DIALOG_MESSAGE])) {
            $this->censor->checkImage($cosUrl, true, $tmpFileWithExt);
            if ($this->censor->isMod) {
                $this->outPut(ResponseCode::NOT_ALLOW_CENSOR_IMAGE);
            }
        }

        $mimeType = Util\MimeType::detectByFilename($cosUrl);
        $filePath = substr_replace($fileData['dirname'], '', strpos($fileData['dirname'], '/'), strlen('/')) . '/';
        $attachmentName = urldecode($fileData['basename']);

        if (in_array($data['type'], [Attachment::TYPE_OF_IMAGE, Attachment::TYPE_OF_DIALOG_MESSAGE])) {
            $imageFile = @file_get_contents($cosUrl, false, stream_context_create(['ssl'=>['verify_peer'=>false, 'verify_peer_name'=>false]]));
            $imageSize = imagecreatefromstring($imageFile);
            $width = imagesx($imageSize);
            $height = imagesy($imageSize);

            // 模糊图处理
            if ($data['type'] == Attachment::TYPE_OF_IMAGE) {
                @file_put_contents($tmpFileWithExt, $imageFile);
                $imageFile = new UploadedFile(
                    $tmpFileWithExt,
                    $attachmentName,
                    $mimeType,
                    0,
                    true
                );
                // 帖子图片自适应旋转
                if(strtolower($ext) != 'gif' && extension_loaded('exif')) {
                    $this->image->make($tmpFileWithExt)->orientate()->save();
                }

                $this->uploader->put($data['type'], $imageFile, urldecode($fileData['basename']), $filePath);
            }
        }

        $attachment = new Attachment();
        $attachment->uuid = Str::uuid();
        $attachment->user_id = $this->user->id;
        $attachment->type = $data['type'];
        $attachment->is_approved = Attachment::APPROVED;
        $attachment->attachment = $attachmentName;
        $attachment->file_path = $filePath;
        $attachment->file_name = $data['fileName'];
        $attachment->file_size = $fileSize;
        $attachment->file_width = $width ?? 0;
        $attachment->file_height = $height ?? 0;
        $attachment->file_type = $mimeType;
        $attachment->is_remote = Attachment::YES_REMOTE;
        $attachment->ip = ip($this->request->getServerParams());
        $attachment->save();
        @unlink($tmpFile);
        @unlink($tmpFileWithExt);
        $attachment->url = $attachment->thumbUrl = $cosUrl;

        $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($attachment));
    }
}
