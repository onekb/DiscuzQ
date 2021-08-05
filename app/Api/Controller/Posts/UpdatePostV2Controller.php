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

use App\Commands\Post\EditPost;
use App\Common\ResponseCode;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class UpdatePostV2Controller extends DzqController
{
    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    public function main()
    {
        $actor = $this->user;
        $postId= $this->inPut('pid');
        if(empty($postId)) return  $this->outPut(ResponseCode::INVALID_PARAMETER);

        $data = $this->inPut('data',[]);

        if (empty($data)) return $this->outPut(ResponseCode::NET_ERROR);

        $data['type'] = 'posts';
        $post = $this->bus->dispatch(
            new EditPost($postId, $actor, $data)
        );

        $build = [
            'pid' => $postId,
            'content' => $data['attributes']['content'],
            'likeCount' => $post['like_count'],
            'replyCount' => $post['reply_count'],
            'isFirst' => $post['is_first'],
            'isApproved' => $post['is_approved'],
            'updatedAt' => optional($post['updated_at'])->format('Y-m-d H:i:s'),
            'isLiked' => $data['attributes']['isLiked'],
        ];

        $data = $this->camelData($build);

        if ($post->id == $postId) {
            return $this->outPut(ResponseCode::SUCCESS, '',$data);
        }

        return $this->outPut(ResponseCode::NET_ERROR, '', []);
    }

}