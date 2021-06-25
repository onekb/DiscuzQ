<?php
/**
 * Copyright (C) 2021 Tencent Cloud.
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

namespace App\Api\Controller\Posts;

use App\Api\Serializer\PostSerializer;
use App\Commands\Post\EditPost;
use App\Common\ResponseCode;
use App\Models\ThreadUser;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class UpdatePostV2Controller extends DzqController
{
    protected $bus;

    protected $postSerializer;

    public function __construct(
        PostSerializer $postSerializer,
        Dispatcher $bus
    ) {
        $this->postSerializer = $postSerializer;
        $this->bus = $bus;
    }

    public function main()
    {
        $actor = $this->user;
        $postId= $this->inPut('pid');
        if(empty($postId)) return  $this->outPut(ResponseCode::INVALID_PARAMETER);

        $data = $this->inPut('data',[]);

        if (empty($data)) return $this->outPut(ResponseCode::NET_ERROR);
        
        $post = $this->bus->dispatch(
            new EditPost($postId, $actor, $data)
        );
        $threadId = $post['thread_id'];

        $isFavorite = ThreadUser::query()->where('thread_id', $threadId)->where('user_id', $actor->id)->exists();

        $build = [
            'pid' => $postId,
            'threadId'=>$threadId,
            'content' => $data['attributes']['content'] ?? $post['content'],
            'likeCount' => $post['like_count'],
            'replyCount' => $post['reply_count'],
            'isFirst' => $post['is_first'],
            'isApproved' => $post['is_approved'],
            'updatedAt' => optional($post['updated_at'])->format('Y-m-d H:i:s'),
            'isLiked' => $data['attributes']['isLiked'] ?? false,
            'canLike' => $this->user->can('like', $post),
            'canFavorite' => (bool) $this->user->can('favorite',$post),
            'isFavorite' =>  $isFavorite,
            'rewards' => floatval(sprintf('%.2f', $post->getPostReward())),
            'redPacketAmount' => $this->postSerializer->getPostRedPacketAmount($post['id'], $post['thread_id'], $post['user_id']),
        ];

        if ($post->id == $postId) {
            return $this->outPut(ResponseCode::SUCCESS, '',$build);
        }

        return $this->outPut(ResponseCode::NET_ERROR, '', []);
    }

}