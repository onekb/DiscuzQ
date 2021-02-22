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

namespace App\Commands\Topic;

use App\Models\User;
use App\Models\AdminActionLog;
use App\Repositories\TopicRepository;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Foundation\EventsDispatchTrait;
use Illuminate\Support\Arr;

class EditTopic
{
    use AssertPermissionTrait;
    use EventsDispatchTrait;

    /**
     * The ID of the thread to edit.
     *
     * @var int
     */
    public $topicId;

    /**
     * 执行操作的用户.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes to update on the thread.
     *
     * @var array
     */
    public $data;

    /**
     * @param int $threadId The ID of the thread to edit.
     * @param User $actor The user performing the action.
     * @param array $data The attributes to update on the thread.
     */

    public function __construct($topicId, User $actor, array $data)
    {
        $this->topicId = $topicId;
        $this->actor = $actor;
        $this->data = $data;
    }

    public function handle(TopicRepository $topics)
    {
        $topic = $topics->findOrFail($this->topicId);

        $attributes = Arr::get($this->data, 'attributes', []);
        if (isset($attributes['recommended'])) {
            $topic->recommended = (bool)$attributes['recommended'] ? 1 : 0;
            $topic->recommended_at = date('Y-m-d H:m:s', time());
        }

        if($topic->recommended == 1){
            $action_desc = '推荐话题【'. $topic->content .'】';
        }else{
            $action_desc = '取消推荐话题【'. $topic->content .'】';
        }

        $topic->save();

        if($action_desc !== '' && !empty($action_desc)) {
            AdminActionLog::createAdminActionLog(
                $this->actor->id,
                $action_desc
            );
        }

        return $topic;
    }
}
