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

use App\Models\Sequence;
use App\Common\ResponseCode;
use App\Models\Thread;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Base\DzqController;

class UpdateSequenceController extends DzqController
{
    public function __construct(User $actor, SettingsRepository $settings)
    {
        $this->actor = $actor;
        $this->settings = $settings;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $block_thread_ids = $this->inPut('blockThreadIds') ?? '';
        $block_topic_ids = $this->inPut('blockTopicIds') ?? '';
        $block_user_ids = $this->inPut('blockUserIds') ?? '';
        $category_ids = $this->inPut('categoryIds') ?? '';
        $group_ids = $this->inPut('groupIds') ?? '';
        $site_open_sort = $this->inPut('siteOpenSort') ?? 0;
        $thread_ids = $this->inPut('threadIds') ?? '';
        $topic_ids = $this->inPut('topicIds') ?? '';
        $user_ids = $this->inPut('userIds') ?? '';

        $data = [
            'category_ids'=>$category_ids,
            'group_ids'=>$group_ids,
            'user_ids'=>$user_ids,
            'topic_ids'=>$topic_ids,
            'thread_ids'=>$thread_ids,
            'block_user_ids'=>$block_user_ids,
            'block_topic_ids'=>$block_topic_ids,
            'block_thread_ids'=>$block_thread_ids,
        ];

        $this->settings->set('site_open_sort', $site_open_sort, 'default');

        Sequence::query()->delete();
        Sequence::query()->insert($data);
        return $this->outPut(ResponseCode::SUCCESS);
    }
}
