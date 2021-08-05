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

use App\Models\Post;
use Tobscure\JsonApi\Relationship;

class CommentPostSerializer extends BasicPostSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'comment-posts';

    /**
     * {@inheritdoc}
     *
     * @param Post $model
     */
    public function getDefaultAttributes($model, $user = null)
    {
        $attributes = parent::getDefaultAttributes($model, $user);

        $attributes['isFirst'] = false;
        $attributes['isComment'] = (bool) $model->is_comment;
        if ($likeState = $model->likeState) {
            $attributes['isLiked'] = true;
            $attributes['likedAt'] = $likeState->created_at->format('Y-m-d H:i:s');
        } else {
            $attributes['isLiked'] = false;
        }
        return $attributes;
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function lastThreeComments($post)
    {
        return $this->hasMany($post, CommentPostSerializer::class);
    }
}
