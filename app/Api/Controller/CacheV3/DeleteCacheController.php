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

namespace App\Api\Controller\CacheV3;

use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Discuz\Base\DzqLog;

class DeleteCacheController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有清理缓存的权限');
        }
        return true;
    }

    public function main()
    {
        try {
            app('cache')->clear();
        } catch (\Exception $e) {
             DzqLog::error('cache_clear_failure', [$e->getMessage()]);
             return $this->outPut(ResponseCode::INTERNAL_ERROR, '清空缓存失败！');
        }
        return $this->outPut(ResponseCode::SUCCESS, '缓存清空完毕');
    }
}
