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

class OpenViewCountController extends DzqController
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
        //阅读数计算方式：1：仅点击帖子详情页增加阅读数，0：操作首页帖子、进入详情页增加阅读数
        $openViewCount = Setting::query()->where('key', '=', 'open_view_count')->first();
        $value = !empty($this->inPut('openViewCount')) ? 1 : 0;
        if (! empty($openViewCount)) {
            try {
                Setting::modifyValue('open_view_count', $value);

                DzqCache::delKey(CacheKey::SETTINGS);

                $res = Setting::query()->where('key', '=', 'open_view_count')->first();

                $this->outPut(ResponseCode::SUCCESS, '' ,$res);
            } catch (\Exception $e) {
                DzqLog::error('open_view_count_insert_error' ,['errorMessage' => $e->getMessage()]);
                $this->outPut(ResponseCode::INTERNAL_ERROR, '阅读数计算方式开关异常');
            }
        } else {
            try {
                $res = Setting::query()->insert(['key' => 'open_view_count', 'value' => $value, 'tag' => 'default']);
                if ($res == true) {
                    DzqCache::delKey(CacheKey::SETTINGS);
                    $res = Setting::query()->where('key', '=', 'open_view_count')->first();
                    $this->outPut(ResponseCode::SUCCESS, 'open_view_count 数据添加成功', $res);
                } else {
                    $this->outPut(ResponseCode::INTERNAL_ERROR, 'open_view_count 数据添加失败');
                }
            } catch (\Exception $e) {
                DzqLog::error('open_view_count_insert_error' ,['errorMessage' => $e->getMessage()]);
                $this->outPut(ResponseCode::INTERNAL_ERROR, '阅读数计算方式数据添加异常');
            }
        }
    }
}
