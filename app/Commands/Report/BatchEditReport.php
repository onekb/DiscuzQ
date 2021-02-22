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

use App\Events\Report\Saving;
use App\Models\Report;
use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use App\Models\AdminActionLog;
use Discuz\Foundation\EventsDispatchTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class BatchEditReport
{
    use EventsDispatchTrait;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes to update on the report.
     *
     * @var int
     */
    public $data;

    /**
     * @param User $actor
     * @param array $data
     */
    public function __construct(User $actor, array $data)
    {
        $this->actor = $actor;
        $this->data = $data;
    }

    /**
     * @param Dispatcher $events
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public function handle(Dispatcher $events)
    {
        $this->events = $events;

        $query = Report::query();

        $report = $query->find(Arr::get($this->data, 'id'));

        if (Arr::has($this->data, 'attributes.status')) {
            $report->status = Arr::get($this->data, 'attributes.status');
        }

        $this->events->dispatch(
            new Saving($report, $this->actor, $this->data)
        );

        if($report->post_id !== 0){
            $postDetail = Post::query()->where('id', $report->post_id)->first();
            $action_desc = '标记举报评论/回复【'. $postDetail['content'] .'】为已处理';
        }else{
            $threadDetail = Thread::query()->where('id', $report->thread_id)->first();
            if($threadDetail['title'] !== '' && !empty($threadDetail['title'])){
                $action_desc = '标记举报用户主题帖【'. $threadDetail['title'] .'】为已处理';
            }else{
                $action_desc = '标记举报用户主题帖，ID为【'. $report->thread_id .'】为已处理';
            }

        }

        $report->save();

        if($action_desc !== '' && !empty($action_desc)) {
            AdminActionLog::createAdminActionLog(
                $this->actor->id,
                $action_desc
            );
        }

        $this->dispatchEventsFor($report, $this->actor);

        return $report;
    }
}
