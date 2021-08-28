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
use Discuz\Contracts\Setting\SettingsRepository;

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
        $settings = app()->make(SettingsRepository::class);
        $qcloudSettings = [
            'qcloud_cos_bucket_name' => $settings->get('qcloud_cos_bucket_name', 'qcloud'),
            'qcloud_cos_bucket_area' => $settings->get('qcloud_cos_bucket_area', 'qcloud'),
            'qcloud_secret_id' => $settings->get('qcloud_secret_id', 'qcloud'),
            'qcloud_secret_key' => $settings->get('qcloud_secret_key', 'qcloud'),
            'qcloud_cos' => $settings->get('qcloud_cos', 'qcloud'),
            'support_img_ext' => $settings->get('support_img_ext', 'default'),
            'support_file_ext' => $settings->get('support_file_ext', 'default'),
            'support_max_size' => $settings->get('support_max_size', 'default'),
            'qcloud_cors_origin' => $settings->get('qcloud_cors_origin', 'qcloud')
        ];
        return $qcloudSettings;
    }

    public function checkAttachmentExt($type, $fileExt)
    {
        $settings = $this->getSettings();
        if (in_array($type, [Attachment::TYPE_OF_IMAGE, Attachment::TYPE_OF_DIALOG_MESSAGE])) {
            $ext = $settings['support_img_ext'];
        } else {
            $ext = $settings['support_file_ext'];
        }

        if (!in_array($fileExt, explode(',', $ext))) {
            $this->outPut(ResponseCode::INTERNAL_ERROR, "暂时不支持{$fileExt}类型文件，仅支持{$ext}类型文件");
        }

        return $fileExt;
    }

    public function checkAttachmentSize($fileSize)
    {
        $settings = $this->getSettings();
        $maxSize = $settings['support_max_size'] * 1024 * 1024;
        if ($fileSize > $maxSize) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, "您的文件尺寸超过了站点所支持的最大尺寸({$settings['support_max_size']}MB)");
        }
    }

    public function getAttachmentMimeType($cosUrl)
    {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt ( $ch, CURLOPT_URL, $cosUrl );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        return curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    }

    public function getImageInfo($cosUrl, $censor)
    {
        $newCosUrl = strstr($cosUrl,'?') ? $cosUrl . '&imageInfo' : $cosUrl . '?imageInfo';
        $thumbParameter = 'imageMogr2/thumbnail/' . Attachment::FIX_WIDTH . 'x' . Attachment::FIX_WIDTH;
        $thumbUrl = strstr($cosUrl,'?') ? $cosUrl . '&' . $thumbParameter : $cosUrl . '?' . $thumbParameter;
        $imageInfo = $this->getFileContents($newCosUrl);
        $censor->checkImage($thumbUrl, true);
        if (!$imageInfo) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '未获取到文件信息！');
        }
        $imageInfo = json_decode($imageInfo, true);
        $fileData = $this->getFileData($cosUrl);
        return [
            'width' => $imageInfo['width'],
            'height' => $imageInfo['height'],
            'fileSize' => $imageInfo['size'],
            'ext' => $imageInfo['format'],
            'filePath' => $fileData['filePath'],
            'attachmentName' => $fileData['attachmentName'],
            'thumbUrl' => $thumbUrl
        ];
    }

    public function getDocumentInfo($cosUrl)
    {
        $documentInfo = $this->getFileContents($cosUrl);
        if(!$documentInfo) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '未获取到文件信息！');
        }
        $fileData = $this->getFileData($cosUrl);
        return [
            'width' => 0,
            'height' => 0,
            'fileSize' => strlen($documentInfo),
            'ext' => $fileData['extension'],
            'filePath' => $fileData['filePath'],
            'attachmentName' => $fileData['attachmentName'],
            'thumbUrl' => ''
        ];
    }

    public function getFileData($cosUrl)
    {
        $fileData = parse_url($cosUrl);
        $fileData = pathinfo($fileData['path']);
        $fileData['filePath'] = substr_replace($fileData['dirname'], '', strpos($fileData['dirname'], '/'), strlen('/')) . '/';
        $fileData['attachmentName'] = urldecode($fileData['basename']);
        return $fileData;
    }

    public function getFileContents($url)
    {
        $fileContents = @file_get_contents($url, false, stream_context_set_default(['ssl' => ['verify_peer'=>false, 'verify_peer_name'=>false]]));
        return $fileContents;
    }
}
