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

namespace App\Api\Controller\SettingsV3;

use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\Setting;
use App\Repositories\UserRepository;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Discuz\Base\DzqLog;

class OpenApiLogController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            $this->outPut(ResponseCode::UNAUTHORIZED,'非管理员无权限访问接口层日志接口');
        }
        return true;
    }

    public function main()
    {
        $openApiLog = Setting::query()->where('key', '=', 'open_api_log')->first();

        if (! empty($openApiLog)) {
            try {
                $openApiLog['value'] == 0 ? $value = 1 : $value = 0;
                Setting::modifyValue('open_api_log', $value);

                DzqCache::delKey(CacheKey::SETTINGS);

                $res = Setting::query()->where('key', '=', 'open_api_log')->first();

                $this->outPut(ResponseCode::SUCCESS, '' ,$res);
            } catch (\Exception $e) {
                DzqLog::error('open_api_log_insert_error' ,['errorMessage' => $e->getMessage()]);
                $this->outPut(ResponseCode::INTERNAL_ERROR, '接口层日志开关异常');
            }
        } else {
            try {
                $res = Setting::query()->insert(['key' => 'open_api_log', 'value' => 1, 'tag' => 'default']);
                if ($res == true) {
                    $this->outPut(ResponseCode::SUCCESS, 'open_api_log 数据添加成功');
                } else {
                    $this->outPut(ResponseCode::INTERNAL_ERROR, 'open_api_log 数据添加失败');
                }
            } catch (\Exception $e) {
                DzqLog::error('open_api_log_insert_error' ,['errorMessage' => $e->getMessage()]);
                $this->outPut(ResponseCode::INTERNAL_ERROR, '接口层日志数据添加异常');
            }
        }
    }
}
