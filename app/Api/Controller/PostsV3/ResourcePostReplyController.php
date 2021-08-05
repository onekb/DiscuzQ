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

namespace App\Api\Controller\PostsV3;


use App\Api\Serializer\AttachmentSerializer;
use App\Api\Serializer\CommentPostSerializer;
use App\Common\ResponseCode;
use App\Models\Attachment;
use App\Models\Post;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class ResourcePostReplyController extends DzqController
{
    //返回的数据一定包含的数据
    public $include = [
        'user:id,username,avatar',
        'user.groups:id,name,is_display',
        'likedUsers:id,username,avatar',
        'images'
    ];

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }

        $post = Post::find($this->inPut('pid'));
        if (!$post) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }
        return $userRepo->canViewThreadDetail($this->user, $post->thread);
    }

    public function main()
    {
        $coment_post_serialize = $this->app->make(CommentPostSerializer::class);
        $attachment_serialize = $this->app->make(AttachmentSerializer::class);

        $post_id = $this->inPut('pid');
        if(empty($post_id))       return  $this->outPut(ResponseCode::INVALID_PARAMETER );
        $comment_post = Post::find($post_id);

        if(empty($comment_post))          return  $this->outPut(ResponseCode::INVALID_PARAMETER);
        if($comment_post->if_first || $comment_post->is_comment || $comment_post->thread->deleted_at){
            return $this->outPut(ResponseCode::NET_ERROR);
        }
        $include = !empty($this->inPut('include')) ? array_unique(array_merge($this->include, explode(',', $this->inPut('include')))) : $this->include;
        $comment_post->loadMissing($include);
        Post::setStateUser($this->user);
        $data = $coment_post_serialize->getDefaultAttributes($comment_post, $this->user);

        //获取最新评论回复数据
        $replyData = [];
        if(intval($data['replyCount']) > 0){
            $replyId = Post::query()
                ->where('reply_post_id',$post_id)
                ->whereNull("deleted_at")
                ->where('is_comment', true)
                ->orderBy('id','desc')
                ->limit(1)
                ->first(['id','user_id','reply_user_id','comment_user_id']);
            $replyIdArr = $replyId->toArray();
            $user_id = $replyIdArr['user_id'];
            $users = $this->getUser($user_id);
            $reply_user_id = $replyIdArr['reply_user_id'];
            $replyUsers = $this->getUser($reply_user_id);
            $comment_user_id =  $replyIdArr['comment_user_id'];
            $commentUsers = $this->getUser($comment_user_id);
            $comment_post_id = $replyIdArr['id'];
            $attachments = $this->getAttachment($comment_post_id,$attachment_serialize);
            $comment_post_collect = Post::query()->where('id', $comment_post_id)->first();
            $replyData = $coment_post_serialize->getDefaultAttributes($comment_post_collect,$this->user);
            $replyData['user'] = $users;
            $replyData['replyUser'] = $replyUsers;
            $replyData['commentUser'] = !empty($commentUsers) ? $commentUsers : null;
            $replyData['attachments'] = !empty($attachments) ? $attachments : null;
        }
        return $this->outPut(ResponseCode::SUCCESS,'', $replyData);
    }

    protected function getUser($userId)
    {
        if (!$userId) {
            return null;
        }
        $users = User::query()->where('id', $userId)->first(['id','nickname','avatar','realname'])->toArray();
        return $users;
    }

    protected function getAttachment($ids,$attachmentSerializer)
    {
        if (!$ids) {
            return null;
        }
        $attachments = Attachment::query()->where('type_id', $ids)->get();
        $attachments = $attachments
            ->map(function (Attachment $attachment) use ($attachmentSerializer) {
                return $attachmentSerializer->getDefaultAttributes($attachment);
            });
        $attachments = $attachments->toArray();
        return $attachments;
    }


}
