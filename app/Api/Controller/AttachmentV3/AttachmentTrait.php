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

namespace App\Api\Controller\AttachmentV3;

use App\Common\ResponseCode;
use App\Models\Attachment;
use App\Models\Setting;

trait AttachmentTrait
{
    public function checkUploadAttachmentPermissions($type, $user, $userRepo)
    {
        if ($type == Attachment::TYPE_OF_FILE) {
            if (!$userRepo->canInsertAttachmentToThread($user)) $this->outPut(ResponseCode::UNAUTHORIZED,'没有发附件权限');
        } else if ($type == Attachment::TYPE_OF_IMAGE) {
            if (!$userRepo->canInsertImageToThread($user)) $this->outPut(ResponseCode::UNAUTHORIZED,'没有发图片权限');
        } else if ($type == Attachment::TYPE_OF_AUDIO) {
            if (!$userRepo->canInsertVideoToThread($user)) $this->outPut(ResponseCode::UNAUTHORIZED,'没有发视频权限');
        } else if ($type == Attachment::TYPE_OF_VIDEO) {
            if (!$userRepo->canInsertAudioToThread($user)) $this->outPut(ResponseCode::UNAUTHORIZED,'没有发音频权限');
        } else if ($type == Attachment::TYPE_OF_DIALOG_MESSAGE) {
            if (!$userRepo->canCreateDialog($user)) $this->outPut(ResponseCode::UNAUTHORIZED,'没有发私信权限');
        } else {
            $this->outPut(ResponseCode::INVALID_PARAMETER);
        }
    }

    public function getSettings()
    {
        return Setting::query()
            ->whereIn('key', [
                'qcloud_cos_bucket_name', 'qcloud_cos_bucket_area',
                'qcloud_secret_id', 'qcloud_secret_key',
                'support_img_ext', 'support_file_ext',
                'support_max_size', 'qcloud_cos'])
            ->get(['key', 'value'])
            ->pluck('value','key')
            ->toArray();
    }

    public function checkAttachmentExt($type, $fileName)
    {
        $settings = $this->getSettings();
        if (in_array($type, [Attachment::TYPE_OF_IMAGE, Attachment::TYPE_OF_DIALOG_MESSAGE])) {
            $ext = $settings['support_img_ext'];
        } else {
            $ext = $settings['support_file_ext'];
        }
        $fileExt = explode('.', $fileName);
        if (!isset($fileExt[1])) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '上传文件后缀名有错误');
        }
        if (!in_array($fileExt[1], explode(',', $ext))) {
            $this->outPut(ResponseCode::INTERNAL_ERROR, "暂时不支持{$fileExt[1]}类型文件，仅支持{$ext}类型文件");
        }
        return $fileExt[1];
    }

    public function checkAttachmentSize($fileSize)
    {
        $settings = $this->getSettings();
        $maxSize = $settings['support_max_size'] * 1024 * 1024;
        if ($fileSize > $maxSize) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, "您的文件尺寸超过了站点所支持的最大尺寸({$settings['support_max_size']}MB)");
        }
    }
}
