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

use App\Api\Controller\ThreadsV3\ThreadHelper;
use App\Common\CacheKey;
use App\Formatter\Formatter;
use App\Models\Post;
use App\Models\Thread;
use App\Traits\HasPaidContent;
use App\Repositories\UserRepository;
use Discuz\Api\Serializer\AbstractSerializer;
use Discuz\Base\DzqCache;
use s9e\TextFormatter\Utils;
use Tobscure\JsonApi\Relationship;

class BasicPostSerializer extends AbstractSerializer
{
    use HasPaidContent;

    /**
     * {@inheritdoc}
     */
    protected $type = 'posts';

    /**
     * @param UserRepository $userRepo
     */
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * {@inheritdoc}
     *
     * @param Post $model
     */
    protected function getDefaultAttributes($model, $user = null)
    {
        $this->paidContent($model);

        // 插入文中的图片及附件 ID
        $contentAttachIds = collect(
            Utils::getAttributeValues($model->getRawOriginal('content'), 'IMG', 'title')
        )->merge(
            Utils::getAttributeValues($model->getRawOriginal('content'), 'URL', 'title')
        )->unique()->values();

        $attributes = [
            'id'                => $model->id,
            'userId'            => $model->user_id,
            'replyPostId'       => $model->reply_post_id,
            'replyUserId'       => $model->reply_user_id,
            'commentPostId'     => $model->comment_post_id,
            'commentUserId'     => $model->comment_user_id,
//            'summaryText'       => str_replace(['<t><p>', '</p></t>'], ['', ''],$model->summary_text),
//            'content'           => str_replace(['<t><p>', '</p></t>'], ['', ''],$model->content),
            'summaryText'       =>  $model->summary_text,
            'content'           =>  $model->content,
            'replyCount'        => (int) $model->reply_count,
            'likeCount'         => (int) $model->like_count,
            'createdAt'         => optional($model->created_at)->format('Y-m-d H:i:s'),
            'updatedAt'         => optional($model->updated_at)->format('Y-m-d H:i:s'),
            'isApproved'        => (int) $model->is_approved,
            'canApprove'        => empty($user) ? false : $user->isAdmin(),
            'canDelete'         => empty($user) ? false : $user->isAdmin(),
            'canHide'           => empty($user) ? false : $this->userRepo->canHidePost($user, $model),
            'contentAttachIds'  => $contentAttachIds,
        ];

        //更新 content 中的图片url，这里新增一个字段主要用户编辑
        $attributes['parseContentHtml'] = $attributes['content'];
        if(!empty($model->parseContentHtml)){
            $attributes['parseContentHtml'] = $model->parseContentHtml;
        }

        //获取评论对应的帖子类型，如果是老数据就走 s9e，新数据就走封装方法
        $thread = DzqCache::hGet(CacheKey::LIST_THREADS_V3_THREADS, $model->thread_id, function ($threadId) {
            return Thread::getOneThread($threadId, true);
        });
        if($thread['type'] != Thread::TYPE_OF_ALL){
            $attributes['content']  =  app()->make(Formatter::class)->render($model->content);
        }else{
//            $content = str_replace(['<t><p>', '</p></t>'], ['', ''],$model->content);
            //针对新数据格式的 post，使用内部封装方法正则
//            list($searches, $replaces) = ThreadHelper::getThreadSearchReplace($content);
//            $attributes['content'] = str_replace($searches, $replaces, $content);
            $attributes['content'] = $model->content;
        }


        if (!empty($user) && ($user->isAdmin() || $user->id === $model->user_id)) {
            $attributes += [
                'ip'    => $model->ip,
                'port'  => $model->port,
            ];
        }

        if ($model->deleted_at) {
            $attributes['isDeleted'] = true;
            $attributes['deletedAt'] = $this->formatDate($model->deleted_at);
        } else {
            $attributes['isDeleted'] = false;
        }

        Post::setStateUser($user);

        return $attributes;
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function user($post)
    {
        return $this->hasOne($post, UserSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function thread($post)
    {
        return $this->hasOne($post, ThreadSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function replyUser($post)
    {
        return $this->hasOne($post, UserSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function commentUser($post)
    {
        return $this->hasOne($post, UserSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function deletedUser($post)
    {
        return $this->hasOne($post, UserSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function likedUsers($post)
    {
        return $this->hasMany($post, UserSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    public function mentionUsers($post)
    {
        return $this->hasMany($post, UserSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function images($post)
    {
        return $this->hasMany($post, AttachmentSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    protected function attachments($post)
    {
        return $this->hasMany($post, AttachmentSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    public function logs($post)
    {
        return $this->hasMany($post, UserActionLogsSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    public function lastDeletedLog($post)
    {
        return $this->hasOne($post, UserActionLogsSerializer::class);
    }

    /**
     * @param $post
     * @return Relationship
     */
    public function postGoods($post)
    {
        return $this->hasOne($post, PostGoodsSerializer::class);
    }
}
