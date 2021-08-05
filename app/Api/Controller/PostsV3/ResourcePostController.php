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


use App\Api\Controller\ThreadsV3\ThreadHelper;
use App\Api\Serializer\PostSerializer;
use App\Api\Serializer\AttachmentSerializer;
use App\Api\Serializer\CommentPostSerializer;
use App\Common\ResponseCode;
use App\Models\Attachment;
use App\Models\UserWalletLog;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class ResourcePostController extends DzqController
{
    //返回的数据一定包含的数据
    public $include = [
        'user:id,username,avatar',
        'user.groups:id,name,is_display',
        'likedUsers:id,username,avatar',
        'images'
    ];

    protected $postSerializer;

    public function __construct( PostSerializer $postSerializer ) {
        $this->postSerializer = $postSerializer;
    }


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
        /*  暂时不需要缓存
        $cacheKey = CacheKey::POST_RESOURCE_BY_ID.$post_id;
        $cache = app('cache');
        $cacheData = $cache->get($cacheKey);
        if(!empty($cacheData)){
            $cacheRet = unserialize($cacheData);
            return $this->outPut(ResponseCode::SUCCESS,'', $cacheRet);
        }
        */
        $comment_post->loadMissing($include);

        Post::setStateUser($this->user);

        $data = $coment_post_serialize->getDefaultAttributes($comment_post, $this->user);

        $data['rewards'] = floatval(sprintf('%.2f', $comment_post->getPostReward(UserWalletLog::TYPE_INCOME_THREAD_REWARD)));
        $data['redPacketAmount'] = $this->postSerializer->getPostRedPacketAmount($comment_post->id, $comment_post->thread_id, $comment_post->user_id);
        $data['canLike'] = app(UserRepository::class)->canLikePosts($this->user);
        $data['images'] = [];
        $data['likeUsers'] = $comment_post->likedUsers;
        if(!empty($comment_post->images)){
            foreach ($comment_post->images as $key => $val){
                $data['images'][$key] = $attachment_serialize->getDefaultAttributes($val, $this->user);
                $data['images'][$key]['typeId'] = $data['images'][$key]['type_id'];
                unset($data['images'][$key]['type_id']);
            }
        }

        $postUserId =array($data['userId']);
        $data['user'] = $this->getUserWithGroup($postUserId);

        //获取回复评论列表
//        if(intval($data['replyCount']) > 0){
            $replyId = Post::query()
                ->where('reply_post_id',$post_id)
                ->whereNull("deleted_at")
                ->where('is_comment', true)
                ->get(['id','user_id','reply_user_id','comment_user_id']);
            $replyIdArr = $replyId->toArray();

            $user_id = array_unique(array_column($replyIdArr, 'user_id'));
            $users = $this->getUser($user_id);
            $reply_user_id = array_unique(array_column($replyIdArr, 'reply_user_id'));
            $replyUsers = $this->getUser($reply_user_id);
            $comment_user_id = array_unique(array_column($replyIdArr, 'comment_user_id'));
            $commentUsers = $this->getUser($comment_user_id);
            $comment_post_id = array_column($replyIdArr,'id');

            $attachments = $this->getAttachment($comment_post_id,$attachment_serialize);
            $comment_post_collect = Post::query()->whereIn('id', $comment_post_id)->get();

            //触发审核只有管理员和自己能看到
            $i = 0;
            foreach ($comment_post_collect as $k=>$value){
                if ($value['is_approved'] != Post::APPROVED && $this->user->id != $value['user_id'] && !$this->user->isAdmin()) {
                    continue;
                }
                $comment_post_collect[$k]->loadMissing($include);
                $data['commentPosts'][$i] = $coment_post_serialize->getDefaultAttributes($comment_post_collect[$k], $this->user);
                $data['commentPosts'][$i]['user'] = $users[$value['user_id']];
                $data['commentPosts'][$i]['replyUser'] = $replyUsers[$value['reply_user_id']];
                $data['commentPosts'][$i]['commentUser'] = !empty($commentUsers[$value['comment_user_id']]) ? $commentUsers[$value['comment_user_id']]: null;
                $data['commentPosts'][$i]['attachments'] = !empty($attachments[$value['id']]) ? $attachments[$value['id']] : null;
                $i++;
            }

//        }
//        $cache->put($cacheKey, serialize($data), 5*60);
        return $this->outPut(ResponseCode::SUCCESS,'', $data);
    }

    protected function getUser($userIds)
    {
        if (!$userIds) {
            return null;
        }
        $users = User::query()->whereIn('id', $userIds)->get(['id','nickname','avatar','realname'])->toArray();
        $users = array_column($users, null, 'id');
        return $users;
    }

    protected function getAttachment($ids,$attachmentSerializer)
    {
        if (!$ids) {
            return null;
        }
        $attachments = Attachment::query()->whereIn('type_id', $ids)->get();
        $attachments = $attachments
            ->map(function (Attachment $attachment) use ($attachmentSerializer) {
                return $attachmentSerializer->getDefaultAttributes($attachment);
            });
        $attachments = $attachments->toArray();
        $newAttachments = [];
        foreach ($attachments as $k=>$val){
            $newAttachments[$val['type_id']][] = $this->camelData($val);
        }
        return $newAttachments;
    }

    protected function getUserWithGroup($userId)
    {
        if (!$userId) {
            return null;
        }
        $user = User::query()->where('id', $userId)->first(['id','nickname','avatar','realname'])->toArray();
        $groups = GroupUser::instance()->getGroupInfo($userId);
        $groups = array_column($groups, null, 'user_id');
        $user['groups'] = [];
        if($groups){
            $user['groups'] = $this->getGroupInfo($groups[$userId[0]]);
        }
        return $user;
    }

    protected function getGroupInfo($group)
    {
        return [
            'id' => $group['group_id'],
            'name' => $group['groups']['name'],
            'isDisplay' => $group['groups']['is_display']
        ];
    }


}
