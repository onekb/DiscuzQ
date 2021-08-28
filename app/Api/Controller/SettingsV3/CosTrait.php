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

use App\Common\Utils;
use Discuz\Contracts\Setting\SettingsRepository;
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
    private function getCosSettings()
    {
        $settings = app()->make(SettingsRepository::class);
        $this->secretId = $settings->get('qcloud_secret_id', 'qcloud');
        $this->secretKey = $settings->get('qcloud_secret_key', 'qcloud');
        $this->region = $settings->get('qcloud_cos_bucket_area', 'qcloud');
        $this->bucket = $settings->get('qcloud_cos_bucket_name', 'qcloud');
        $this->allowedOrigins = $this->bucket;
        if (empty($this->secretId) || empty($this->secretKey) || empty($this->region) || empty($this->bucket)) {
            app('log')->info('对象存储配置不全，无法配置跨域访问CORS');
            return false;
        }
        $this->siteUrl = Utils::getSiteUrl();
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
        if(!$this->getCosSettings()) {
            return false;
        }
        $cosClient = $this->cosClient;
        $id = 0;
        $urlArray = [];
        $newUrlArray = [];
        $corsRules = [];

        try {
            $oldBucketCors = $this->getBucketCors();
            if (!empty($oldBucketCors) && !empty($oldBucketCors['CORSRules'])) {
                foreach ($oldBucketCors['CORSRules'] as $value) {
                    $urlArray = array_merge($urlArray, $value['AllowedOrigins']);
                }
                // 已写入白名单的不再重复写入
                if (in_array($this->siteUrl, $urlArray)) {
                    app()->make(SettingsRepository::class)->set('qcloud_cors_origin', json_encode($urlArray), 'qcloud');
                    return true;
                }
                $ids = array_column($oldBucketCors['CORSRules'], 'ID');
                $id  = max($ids) + 1;
                $corsRules = $oldBucketCors['CORSRules'];
            }
        } catch (\Exception $e) {
            app('log')->info('未获取到原[跨域访问CORS]名单信息，清空[跨域访问CORS]，重置ID');
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

        // 再检查是否插入成功
        try {
            $newBucketCors = $this->getBucketCors();
            foreach ($newBucketCors['CORSRules'] as $value) {
                $newUrlArray = array_merge($newUrlArray, $value['AllowedOrigins']);
            }
            app()->make(SettingsRepository::class)->set('qcloud_cors_origin', json_encode($newUrlArray), 'qcloud');
            if (!in_array($this->siteUrl, $newUrlArray)) {
                app('log')->info('插入跨域访问CORS名单失败!');
                return false;
            }
            app('log')->info('插入跨域访问CORS名单成功：' . json_encode($newBucketCors['CORSRules']));
            return true;
        } catch (\Exception $e) {
            app('log')->info('插入跨域访问CORS名单失败!');
            return false;
        }
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
