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

namespace App\Api\Controller\SettingsV3;

use App\Models\Setting;
use Qcloud\Cos\Client;

trait CosTrait
{
    private $secretId;
    private $secretKey;
    private $region;
    private $bucket;
    private $allowedOrigins;
    private $siteUrl;
    private $cosClient;

    // 注入settings信息
    private function getSettings()
    {
        $settings = Setting::query()
            ->whereIn('key', ['qcloud_cos_bucket_name','qcloud_cos_bucket_area','qcloud_secret_id','qcloud_secret_key'])
            ->get(['key', 'value'])
            ->pluck('value','key')
            ->toArray();
        $this->secretId = $settings['qcloud_secret_id'];
        $this->secretKey = $settings['qcloud_secret_key'];
        $this->region = $settings['qcloud_cos_bucket_area'];
        $this->bucket = $settings['qcloud_cos_bucket_name'];
        $this->allowedOrigins = $settings['qcloud_cos_bucket_name'];
        $this->siteUrl = $_SERVER['HTTP_ORIGIN'];
        $this->cosClient = new Client(
            array (
                'region' => $this->region,
                'schema' => 'https',
                'credentials'=> array (
                    'secretId'  => $this->secretId,
                    'secretKey' => $this->secretKey
                )
            )
        );
        return $settings;
    }

    // 插入白名单
    public function putBucketCors()
    {
        $this->getSettings();
        $cosClient = $this->cosClient;
        $id = 0;
        $urlArray = [];
        $corsRules = [];

        try {
            $oldBucketCors = $this->getBucketCors();
            if (!empty($oldBucketCors) && !empty($oldBucketCors['CORSRules'])) {
                foreach ($oldBucketCors['CORSRules'] as $value) {
                    $urlArray = array_merge($urlArray, $value['AllowedOrigins']);
                }
                // 已写入白名单的不再重复写入
                if (in_array($this->siteUrl, $urlArray)) {
                    return false;
                }
                $ids = array_column($oldBucketCors['CORSRules'], 'ID');
                $id  = max($ids) + 1;
                $corsRules = $oldBucketCors['CORSRules'];
            }
        } catch (\Exception $e) {
            app('log')->info('未获取到原白名单信息，清空白名单，重置ID');
            $this->deleteBucketCors();
            $id = 1;
        }

        $newCorsRules = [
            'ID' => $id,
            'AllowedHeaders' => array('*'),
            'AllowedMethods' => array('PUT', 'GET', 'POST', 'DELETE', 'HEAD'),
            'AllowedOrigins' => array($this->siteUrl),
            'ExposeHeaders' => array('ETag', 'Content-Length', 'x-cos-request-id'),
            'MaxAgeSeconds' => 300
        ];

        $corsRules[] = $newCorsRules;
        $cosClient->putBucketCors(array(
            'Bucket' => $this->bucket,
            'CORSRules' => $corsRules,
        ));

        $newBucketCors = $this->getBucketCors();
        app('log')->info('插入白名单成功：' . json_encode($newBucketCors['CORSRules']));
        return $newBucketCors;
    }

    // 查看白名单
    public function getBucketCors()
    {
        $cosClient = $this->cosClient;
        return $cosClient->getBucketCors(array(
            'Bucket' => $this->bucket,
        ));
    }

    // 删除白名单
    public function deleteBucketCors()
    {
        $cosClient = $this->cosClient;
        return $cosClient->deleteBucketCors(array(
            'Bucket' => $this->bucket,
        ));
    }
}
