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

namespace App\Traits;

use App\Models\ThreadVideo;
use Discuz\Contracts\Setting\SettingsRepository;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Vod\VodUploadClient;
use Vod\Model\VodUploadRequest;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Vod\V20180717\VodClient;
use TencentCloud\Vod\V20180717\Models\ProcessMediaRequest;

trait VideoCloudTrait
{
    protected $url = 'vod.tencentcloudapi.com';

    protected $setting;

    private function __construct(SettingsRepository $setting) {
        $this->setting = $setting;
    }

    private function videoUpload($userId,$threadId,$mediaUrl){
        $log = app('log');
        if(empty($mediaUrl) || empty($userId) || empty($threadId)){
            $log->info('视频上传参数不能为空');
            return false;
        }
        if (strpos($mediaUrl, '?') !== false) {
            $media = explode("?",$mediaUrl);
            $media = $media[0];
            $ext = substr($media,strrpos($media,'.')+1);
        } else {
            $ext = substr($mediaUrl,strrpos($mediaUrl,'.')+1);
        }

        $localFlie = Str::random(40).".".$ext;
        $absoluteUrl = storage_path('tmp/').$localFlie;
        $fileData = @file_get_contents($mediaUrl,false, stream_context_create(['ssl'=>['verify_peer'=>false, 'verify_peer_name'=>false]]));
        if(!$fileData){
            $log->info('媒体文件不存在');
            return false;
        }
        $tempFlie = @file_put_contents($absoluteUrl,$fileData);
        if(!$tempFlie){
            $log->info('下载视频失败');
            return false;
        }

        $secretId = $this->settings->get('qcloud_secret_id', 'qcloud');
        $secretKey = $this->settings->get('qcloud_secret_key', 'qcloud');
        $region = $this->settings->get('qcloud_cos_bucket_area','qcloud');

        if(empty($secretId) || empty($secretKey)){
            $log->info('云点播配置不能为空');
            return false;
        }
        if(!file_exists($absoluteUrl)){
            $log->info('本地临时文件不能为空');
            return false;
        }
        $client = new VodUploadClient($secretId, $secretKey);
        $req = new VodUploadRequest();
        $req->MediaFilePath = $absoluteUrl;
        try {
            $rsp = $client->upload($region, $req);
        } catch (\Exception $e) {
            // 处理上传异常
            $log->info('上传视频接口报错', $e->getMessage());
            return false;
        }
        $fileId = $rsp->FileId;
        $mediaUrl = !empty($this->getMediaUrl($rsp->MediaUrl)) ? $this->getMediaUrl($rsp->MediaUrl) : "";
        //删除临时文件
        unlink($absoluteUrl);
        //保存数据库
        $videoId = $this->save($fileId,$mediaUrl,$userId,$threadId,$log);
        //执行转码任务
        $this->processMedia($fileId,$log);
        if($videoId){
            return ['videoId'=>$videoId,'fileId'=>$fileId,'mediaUrl'=>$mediaUrl];
        }else{
            return false;
        }
    }

    private function getMediaUrl($mediaUrl)
    {
        $urlKey = $this->settings->get('qcloud_vod_url_key', 'qcloud');
        $urlExpire = (int)$this->settings->get('qcloud_vod_url_expire', 'qcloud');
        if ($urlKey  && !empty($mediaUrl)) {
            $currentTime = Carbon::now()->timestamp;
            $dir = Str::beforeLast(parse_url($mediaUrl)['path'], '/') . '/';
            $t = dechex($currentTime + $urlExpire);
            $us = Str::random(10);
            $sign = md5($urlKey . $dir . $t . $us);
            $mediaUrl = $mediaUrl . '?t=' . $t . '&us=' . $us . '&sign=' . $sign;
        }
        return $mediaUrl;
    }

    //保存到数据库
    private function save($fileId,$mediaUrl,$userId,$threadId,$log){
        if(empty($fileId)){
            return false;
        }
        try {
            $threadVideo = new ThreadVideo();
            $threadVideo->file_id = $fileId;
            $threadVideo->media_url = $mediaUrl;
            $threadVideo->thread_id = $threadId;
            $threadVideo->post_id = 0;
            $threadVideo->user_id = $userId;
            $threadVideo->save();
            return $threadVideo->id;
        }catch (\Exception $e){
            $log->info('数据库异常', $e->getMessage());
            return false;
        }
    }

    //转码
    private function processMedia($fileId,$log){
        if(empty($fileId)){
            return false;
        }
        try {
            $secretId = $this->settings->get('qcloud_secret_id', 'qcloud');
            $secretKey = $this->settings->get('qcloud_secret_key', 'qcloud');

            $cred = new Credential($secretId, $secretKey);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint($this->url);

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new VodClient($cred, "", $clientProfile);
            $req = new ProcessMediaRequest();
            $params = array(
                'FileId'=>$fileId
            );
            $req->fromJsonString(json_encode($params));
            $resp = $client->ProcessMedia($req);
            $resp = json_decode($resp->toJsonString(),true);
            if(empty($resp['TaskId'])){
                $log->info('转码任务未执行');
            }
            return true;
        }catch (\Exception $e){
            $log->info('转码异常', $e->getMessage());
            return false;
        }
    }
}
