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

use App\Commands\Post\CreatePost;
use App\Common\ResponseCode;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;

class CreatePostV2Controller extends DzqController
{
    use AssertPermissionTrait;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    public function main()
    {
        $actor = $this->user;
        $data = [
            'content' => $this->inPut('content'),
            'isComment' => $this->inPut('isComment'),
            'replyId' => $this->inPut('replyId'),
            'replyUserId' => $this->inPut('replyUserId'),
            'commentPostId' => $this->inPut('commentPostId'),
            'commentUserId' => $this->inPut('commentUserId'),
        ];

        if(empty($data['content'])){
            $this->outPut(ResponseCode::INVALID_PARAMETER, '内容不能为空');
        }

        if(empty($data['replyId'])){
            unset($data['replyId']);
        }

        if(empty($data['commentPostId'])){
            unset($data['commentPostId']);
        }

        $requestData = [
            "type" => "posts",
            "relationships" =>  [
                "thread" =>[
                "data" =>  [
                    "type" => "threads",
                    'id' => $this->inPut('id'),
                    ]
                ],
            ]
        ];

        $threadId = $this->inPut('id');
        if(!empty($this->inPut('attachments'))){
            $attachments = $this->inPut('attachments');
            foreach ($attachments as $k=>$val){
                $requestData['relationships']['attachments']['data'][$k]['id'] = (string)$val['id'];
                $requestData['relationships']['attachments']['data'][$k]['type'] = $val['type'];
            }
        }
        $requestData['attributes'] = $data;

        $ip = ip($this->request->getServerParams());
        $port = Arr::get($this->request->getServerParams(), 'REMOTE_PORT', 0);
        $result = $this->bus->dispatch(
            new CreatePost($threadId, $actor, $requestData, $ip, $port)
        );


        $build = [
            'id' => $result->id,
            'threadId' => $result->thread_id,
            'userId' => $result->user_id,
            'replyPostId' => $result->reply_post_id,
            'replyUserId' => $result->reply_user_id,
            'commentPostId' => $result->comment_post_id,
            'commentUserId' => $result->comment_user_id,
            'content' => $result->content,
            'isFirst' => $result->is_first,
            'isApproved' => $result->is_approved,
            'isComment'   =>$result->is_comment,
            'createdAt' => optional($result->created_at)->format('Y-m-d H:i:s'),
        ];

        $data = $this->camelData($build);

        return $this->outPut(ResponseCode::SUCCESS,'',$data);

    }

}