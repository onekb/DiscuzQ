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

use App\Models\ThreadReward;
use App\Traits\HasPaidContent;
use Discuz\Api\Serializer\AbstractSerializer;
use Tobscure\JsonApi\Relationship;

class ThreadRewardSerializer extends AbstractSerializer
{
    use HasPaidContent;

    /**
     * {@inheritdoc}
     */
    protected $type = 'everybody_question';

    /**
     * {@inheritdoc}
     *
     * @param Question $model
     */
    public function getDefaultAttributes($model)
    {
        $this->paidContent($model);

        return [
            'thread_id'             => $model->thread_id,
            'post_id'               => $model->post_id,
            'type'                  => $model->type,
            'user_id'               => $model->thread->is_anonymous ? 0 : $model->user_id, // 判断是否是匿名
            'answer_id'             => $model->answer_id,
            'money'                 => $model->money,
            'remain_money'          => $model->remain_money,
            'created_at'            => $model->created_at,
            'updated_at'            => $model->updated_at,
            'expired_at'            => $model->expired_at
        ];
    }

    /**
     * @param Question $model
     * @return Relationship
     */
    public function beUser($model)
    {
        return $this->hasOne($model, UserSerializer::class);
    }

    /**
     * @param Question $model
     * @return Relationship
     */
    protected function images($model)
    {
        return $this->hasMany($model, AttachmentSerializer::class);
    }

}
