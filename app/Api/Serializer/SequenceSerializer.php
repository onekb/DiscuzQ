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

namespace App\Api\Serializer;

use App\Models\Sequence;
use App\Models\Category;
use App\Models\Group;
use App\Models\User;
use App\Models\Topic;
use App\Models\Thread;
use Discuz\Api\Serializer\AbstractSerializer;
use Tobscure\JsonApi\Relationship;
use Illuminate\Database\Eloquent\Builder;

class SequenceSerializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'sequence';

    /**
     * @param Sequence $model
     * @return array
     */
    protected function getDefaultAttributes($model)
    {
        return [
            'category_ids'   => $model->category_ids,
            'group_ids'      => $model->group_ids,
            'users'          => $this->usersList($model->user_ids),
            'topics'         => $this->topicsList($model->topic_ids),
            'threads'        => $model->thread_ids,
            'block_users'    => $this->usersList($model->block_user_ids),
            'block_topics'   => $this->topicsList($model->block_topic_ids),
            'block_threads'  => $model->block_thread_ids
        ];
    }

    /**
     * @param $usersList
     * @return array
     */
    public function usersList($user_ids)
    {
        if(!empty($user_ids)){
            $usersCheckList = User::query()->whereIn('id', explode(',', $user_ids))->orderBy('id')->get();
        }else{
            $usersCheckList = array();
        }
        return $usersCheckList;
    }

    /**
     * @param $topicsList
     * @return array
     */
    public function topicsList($topic_ids)
    {
        if(!empty($topic_ids)){
            $topicsCheckList = Topic::query()->whereIn('id', explode(',', $topic_ids))->orderBy('id')->get();
        }else{
            $topicsCheckList = array();
        }
        return $topicsCheckList;
    }
}
