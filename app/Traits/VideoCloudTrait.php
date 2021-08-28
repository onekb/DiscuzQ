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

    private function videoUpload($userId,$threadId,$mediaUrl,$setting){
        $log = app('log');
        if(empty($mediaUrl) || empty($userId) || empty($threadId) || empty($setting)){
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

        $fileData = $this->doCurlGetRequest($mediaUrl);

        if(!$fileData){
            $log->info('媒体文件不存在');
            return false;
        }
        $tempFlie = @file_put_contents($absoluteUrl,$fileData);
        if(!$tempFlie){
            $log->info('下载视频失败');
            return false;
        }

        $secretId = $setting->get('qcloud_secret_id', 'qcloud');
        $secretKey = $setting->get('qcloud_secret_key', 'qcloud');
        $region = $setting->get('qcloud_cos_bucket_area','qcloud');

        if(empty($secretId) || empty($secretKey)){
            unlink($absoluteUrl);
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
            unlink($absoluteUrl);
            $log->info('上传视频接口报错');
            return false;
        }
        $fileId = $rsp->FileId;
        $mediaUrl = !empty($this->getMediaUrl($rsp->MediaUrl,$setting)) ? $this->getMediaUrl($rsp->MediaUrl,$setting) : "";
        //删除临时文件
        unlink($absoluteUrl);
        //保存数据库
        $videoId = $this->save($fileId,$mediaUrl,$userId,$threadId,$log);
        //执行转码任务
        $process = $this->processMedia($fileId,$log,$setting);
        if($videoId && $process){
            return $videoId;
        }else{
            $log->info('videoId返回失败');
            return false;
        }
    }

    /**
     * @desc 封装curl的调用接口，get的请求方式
     */
    private function doCurlGetRequest($url) {
        ini_set("memory_limit","-1");
        // 创建一个新 cURL 资源
        $ch = curl_init();
        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url); // 需要获取的 URL 地址，也可以在 curl_init() 初始化会话的时候。
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HEADER, false); // 启用时会将头文件的信息作为数据流输出。
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); // 在尝试连接时等待的秒数。设置为 0，则无限等待。
        curl_setopt($ch, CURLOPT_TIMEOUT, 0); // 允许 cURL 函数执行的最长秒数。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // TRUE 将 curl_exec() 获取的信息以字符串返回，而不是直接输出。
        // 抓取 URL 并把它传递给浏览器
        $ret = curl_exec($ch);
        // 关闭 cURL 资源，并且释放系统资源
        curl_close($ch);
        return $ret;
    }

    private function getMediaUrl($mediaUrl,$setting)
    {
        $urlKey = $setting->get('qcloud_vod_url_key', 'qcloud');
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
            $log->info('保存数据库时fileId不能为空');
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
            $log->info('数据库异常');
            return false;
        }
    }

    //转码
    private function processMedia($fileId,$log,$setting){
        if(empty($fileId)){
            $log->info('转码时fileId不能为空');
            return false;
        }
        try {
            $secretId = $setting->get('qcloud_secret_id', 'qcloud');
            $secretKey = $setting->get('qcloud_secret_key', 'qcloud');

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
                return false;
            }
            return true;
        }catch (\Exception $e){
            $log->info('转码异常');
            return false;
        }
    }
}
