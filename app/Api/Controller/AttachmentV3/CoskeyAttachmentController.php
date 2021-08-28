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
use App\Models\Setting;
use App\Repositories\UserRepository;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use QCloud\COSSTS\Sts;
use QCloud\COSSTS\Scope;

class CoskeyAttachmentController extends DzqController
{
    private $sts;
    private $url = 'https://sts.tencentcloudapi.com/';
    private $domain = 'sts.tencentcloudapi.com';
    private $proxy = '';
    private $bucket;
    private $region;
    private $secretId;
    private $secretKey;
    private $durationSeconds = 1800;

    /*
     * Attachment::TYPE_OF_FILE 附件
     * Attachment::TYPE_OF_IMAGE 图片
     * Attachment::TYPE_OF_AUDIO 视频
     * Attachment::TYPE_OF_VIDEO 语音
     */
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $user = $this->user;
        if ($user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }

        $type = (int) $this->inPut('type'); //0 附件 1图片 2视频 3音频
        if ($type == Attachment::TYPE_OF_FILE) {
            if (!$userRepo->canInsertAttachmentToThread($user)) $this->outPut(ResponseCode::UNAUTHORIZED,'没有发附件权限');
        } else if ($type == Attachment::TYPE_OF_IMAGE) {
            if (!$userRepo->canInsertImageToThread($user)) $this->outPut(ResponseCode::UNAUTHORIZED,'没有发图片权限');
        } else if ($type == Attachment::TYPE_OF_AUDIO) {
            if (!$userRepo->canInsertVideoToThread($user)) $this->outPut(ResponseCode::UNAUTHORIZED,'没有发视频权限');
        } else if ($type == Attachment::TYPE_OF_VIDEO) {
            if (!$userRepo->canInsertAudioToThread($user)) $this->outPut(ResponseCode::UNAUTHORIZED,'没有发音频权限');
        } else {
            $this->outPut(ResponseCode::INVALID_PARAMETER);
        }

        return true;
    }

    public function __construct()
    {
        $this->sts = new Sts();
    }

    public function main()
    {
        $settings = $this->settings();
        $cosParem = $this->configuration($settings);
        $type = $this->inPut('type'); //0 附件 1图片 2视频 3音频

        if (!isset($settings['qcloud_cos_bucket_name']) || empty($settings['qcloud_cos_bucket_name'])) {
            $this->outPut(ResponseCode::INTERNAL_ERROR,'请去管理员后台腾讯云设置对象储存配置所属地域');
        }

        if (!isset($settings['qcloud_cos_bucket_area']) || empty($settings['qcloud_cos_bucket_area'])) {
            $this->outPut(ResponseCode::INTERNAL_ERROR,'请去管理员后台腾讯云设置对象储存配置空间名称');
        }

        if (!isset($settings['qcloud_secret_id']) || empty($settings['qcloud_secret_id'])) {
            $this->outPut(ResponseCode::INTERNAL_ERROR,'请去管理员后台腾讯云设置云API配置Secretid');
        }

        if (!isset($settings['qcloud_secret_key']) || empty($settings['qcloud_secret_key'])) {
            $this->outPut(ResponseCode::INTERNAL_ERROR,'请去管理员后台腾讯云设置云API配置SecretKey');
        }

        if ((!isset($settings['support_file_ext']) || empty($settings['support_file_ext'])) && $type == Attachment::TYPE_OF_FILE) {
            $this->outPut(ResponseCode::INTERNAL_ERROR,'请去管理员后台附件设置支持的文件扩展名');
        }

        if ((!isset($settings['support_img_ext']) || empty($settings['support_img_ext'])) && $type == Attachment::TYPE_OF_IMAGE) {
            $this->outPut(ResponseCode::INTERNAL_ERROR,'请去管理员后台附件设置支持图片扩展名');
        }

        if (in_array($type, [Attachment::TYPE_OF_FILE, Attachment::TYPE_OF_IMAGE])) {
            $config = $this->appendix($settings, $cosParem);
        } else {
            $config = $this->complex($cosParem);
        }

        $tempKeys = $this->sts->getTempKeys($config);
        $this->paramDetectCache($tempKeys);

        $this->outPut(ResponseCode::SUCCESS,'', $tempKeys);
    }


    //查询settings信息
    private function settings()
    {
         return Setting::query()
            ->whereIn('key', ['qcloud_cos_bucket_name','qcloud_cos_bucket_area','qcloud_secret_id','qcloud_secret_key','support_img_ext','support_file_ext'])
            ->get(['key', 'value'])
            ->pluck('value','key')
            ->toArray();
    }

    //配置信息
    private function configuration($settings)
    {
        $this->bucket = $settings['qcloud_cos_bucket_name'];
        $this->region = $settings['qcloud_cos_bucket_area'];
        $this->secretId = $settings['qcloud_secret_id'];
        $this->secretKey = $settings['qcloud_secret_key'];

        return array(
            'url' => $this->url,
            'domain' => $this->domain,
            'proxy' => $this->proxy,
            'region' => $this->region , // 换成 bucket 所在园区
            'secretId' => $this->secretId, // 固定密钥
            'secretKey' => $this->secretKey, // 固定密钥
            'durationSeconds' => $this->durationSeconds, // 密钥有效期

        );
    }

    //附件 图片
    private function appendix($settings, $cosParem)
    {
        $type = $this->inPut('type'); //0 附件 1图片 2视频 3音频
        if ($type == Attachment::TYPE_OF_FILE) {
            $suffix = explode(',', $settings['support_file_ext']);
        } else {
            $suffix = explode(',', $settings['support_img_ext']);
        }

        $config = array();
        $allowPrefix = '/public/attachments/' . date('Y/m/d') . '/';
        foreach ($suffix as $val) {
            array_push($config, new Scope("name/cos:PutObject", $this->bucket, $this->region, $allowPrefix . 'a.' . $val));
        }

        return array_merge($cosParem, ['policy' => $this->sts->getPolicy($config)]);
    }

    //视频 语音
    private function complex($cosParem)
    {
        $type = $this->inPut('type'); //0 附件 1图片 2视频 3音频
        if ($type == Attachment::TYPE_OF_AUDIO) {
            $suffix = 'mp4';
        }else{
            $suffix = 'mp3';
        }
        $allowPrefix = '/public/attachments/'.date('Y/m/d').'/a.'.$suffix;
        $config = [
            'bucket' => $this->bucket,
            'allowPrefix' => $allowPrefix,
            'allowActions' => [
                // 简单上传
                'name/cos:PutObject',
                'name/cos:PostObject',
                // 分片上传
                'name/cos:InitiateMultipartUpload',
                'name/cos:ListMultipartUploads',
                'name/cos:ListParts',
                'name/cos:UploadPart',
                'name/cos:CompleteMultipartUpload'
            ]
        ];

        return array_merge($cosParem, $config);
    }

    //生成缓存临时检测参数
    private function paramDetectCache($tempKeys){
        $user = $this->user;
        $get = DzqCache::get(CacheKey::IMG_UPLOAD_TMP_DETECT);
        $param = ['userId' => $this->user->id, 'createdAt' => time()];
        $get[$user->id][$tempKeys['requestId']] = $param;
        if (DzqCache::CACHE_TTL) {
           app('cache')->put(CacheKey::IMG_UPLOAD_TMP_DETECT, $get);
        }
    }

}
