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

use App\Repositories\ThreadRepository;
use Carbon\Carbon;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;


class ThreadDraftClearCommand extends AbstractCommand
{
    protected $signature = 'clear:thread_draft';

    protected $description = '清理草稿箱中过期的主题';

    protected $app;

    /**
     * @var ThreadRepository
     */
    protected $thread;

    /**
     * AvatarCleanCommand constructor.
     * @param Application $app
     * @param ThreadRepository $thread
     */
    public function __construct(Application $app, ThreadRepository $thread)
    {
        parent::__construct();

        $this->app = $app;
        $this->thread = $thread;
    }

    public function handle()
    {
        //清理草稿箱中超过7天的主题数据
        $threads = $this->thread->query()
            ->where('is_draft', '1')
            ->where('updated_at', '<', Carbon::getWeekendDays())
            ->get();

        foreach ($threads as $thread) {
            //数据库删除
            $thread->delete();
        }

        $this->info('清理草稿箱中过期的主题：'. count($threads));
    }
}
