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
use App\Models\Thread;
use App\Models\ThreadTom;
use App\Models\UserWalletLog;
use Exception;
use Tobscure\JsonApi\Relationship;

class PostSerializer extends BasicPostSerializer
{
    /**
     * {@inheritdoc}
     *
     * @param Post $model
     * @throws Exception
     */
    public function getDefaultAttributes($model, $user = null)
    {
        $attributes = parent::getDefaultAttributes($model);

        $attributes['isFirst'] = (bool) $model->is_first;
        $attributes['isComment'] = false;
        $attributes['rewards'] = $model->rewards;
        $attributes['redPacketAmount'] = $this->getPostRedPacketAmount($model->id, $model->thread_id, $model->user_id);
        if (app()->has('isCalled')) {
            unset($attributes['contentHtml']);
            unset($attributes['content']);
        }
        return $attributes;
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function commentPosts($post)
    {
        return $this->hasMany($post, CommentPostSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function lastThreeComments($post)
    {
        return $this->hasMany($post, CommentPostSerializer::class);
    }

    public function getPostRedPacketAmount($post_id, $thread_id, $user_id)
    {
        $thread = Thread::query()->where('id', $thread_id)->first();
        if (empty($thread)) {
            throw new Exception(trans('post.thread_id_not_null'));
        }

        $redPacketTom = ThreadTom::query()->where('thread_id',$thread_id)
                          ->where('tom_type',106)
                            ->first();

        $redPacketAmount = 0;
        if ($redPacketTom) {
            $redPacketAmount = UserWalletLog::query()
                ->whereIn('change_type', [UserWalletLog::TYPE_INCOME_TEXT, UserWalletLog::TYPE_INCOME_LONG, UserWalletLog::TYPE_REDPACKET_INCOME])
                ->where(['thread_id' => $thread_id, 'post_id'=> $post_id, 'user_id' => $user_id])
                ->sum('change_available_amount');
        }

        return $redPacketAmount;
    }
}
