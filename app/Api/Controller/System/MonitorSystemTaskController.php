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

namespace App\Api\Controller\System;

use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;

class MonitorSystemTaskController extends DzqController
{
    use AssertPermissionTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $get = DzqCache::get(CacheKey::MONITOR_SYSTEM_TASK);
        $arr = ['status' => 0, 'list' => $get];
        if (!empty($get)) {
            $time = time()-strtotime($get[count($get)-1]['time']);
            if ($time <= 60) {
                $arr['status'] = 1;
            }
        }
        return $this->outPut(ResponseCode::SUCCESS,'', $arr);
    }
}
