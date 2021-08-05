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

namespace App\Console\Commands;

use App\Common\CacheKey;
use App\Repositories\InviteRepository;
use Discuz\Base\DzqCache;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;

class MonitorSystemTaskCommand extends AbstractCommand
{
    protected $signature = 'task:start';

    protected $description = '监听定时任务状态';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $get = DzqCache::get(CacheKey::MONITOR_SYSTEM_TASK);
        $get[] = ['time'=>date('Y-m-d H:i:s')];
        DzqCache::set(CacheKey::MONITOR_SYSTEM_TASK, $get);
        $this->info('监听定时任务启动中:'.date('Y-m-d H:i:s'));
    }
}
