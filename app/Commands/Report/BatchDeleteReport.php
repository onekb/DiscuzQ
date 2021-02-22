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

namespace App\Commands\Report;

use App\Events\Report\Deleting;
use App\Models\Report;
use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use App\Models\AdminActionLog;
use Discuz\Foundation\EventsDispatchTrait;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;

class BatchDeleteReport
{
    use EventsDispatchTrait;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The ID of the report to delete.
     *
     * @var int
     */
    public $id;

    /**
     * 暂未用到，留给插件使用
     *
     * @var array
     */
    public $data;

    /**
     * BatchDeleteReport constructor.
     *
     * @param User $actor
     * @param int $id
     * @param array $data
     */
    public function __construct(User $actor, int $id, array $data = [])
    {
        $this->actor = $actor;
        $this->id = $id;
        $this->data = $data;
    }

    /**
     * @param Dispatcher $events
     * @return bool
     * @throws Exception
     */
    public function handle(Dispatcher $events)
    {
        $this->events = $events;

        $query = Report::query();

        $exists = $query->where('id', $this->id)->exists();

        if ($exists) {
            $report = $query->first();

            $this->events->dispatch(
                new Deleting($report, $this->actor)
            );

            if($report['post_id'] !== 0){
                $postDetail = Post::query()->where('id', $report['post_id'])->first();
                $action_desc = '删除举报评论/回复【'. $postDetail['content'] .'】为已处理';
            }else{
                $threadDetail = Thread::query()->where('id', $report['thread_id'])->first();
                if($threadDetail['title'] !== '' && !empty($threadDetail['title'])){
                    $action_desc = '删除举报用户主题帖【'. $threadDetail['title'] .'】为已处理';
                }else{
                    $action_desc = '删除举报用户主题帖，ID为【'. $report->thread_id .'】为已处理';
                }

            }

            $report->delete();

            if($action_desc !== '' && !empty($action_desc)) {
                AdminActionLog::createAdminActionLog(
                    $this->actor->id,
                    $action_desc
                );
            }

            $this->dispatchEventsFor($report, $this->actor);
        }

        return $exists;
    }
}
