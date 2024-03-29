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

namespace App\Listeners\Post;

use App\Events\Post\Created;
use App\Events\Post\Deleted;
use App\Events\Post\Hidden;
use App\Events\Post\PostWasApproved;
use App\Events\Post\Restored;
use App\Events\Post\Revised;
use App\Events\Post\Saved;
use App\Events\Post\Saving;
use App\Events\Thread\Deleting;
use App\Listeners\User\CheckPublish;
use App\Models\Post;
use App\Models\PostGoods;
use App\Models\PostMod;
use App\Models\Thread;
use App\Models\ThreadTopic;
use App\Models\UserActionLogs;
use App\Notifications\Messages\Database\PostMessage;
use App\Notifications\Replied;
use App\Notifications\System;
use App\Traits\PostNoticesTrait;
use Discuz\Api\Events\Serializing;
use Discuz\Auth\AssertPermissionTrait;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class PostListener
{
    use AssertPermissionTrait;
    use PostNoticesTrait;

    /**
     * @var Dispatcher
     */
    private $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    public function subscribe(Dispatcher $events)
    {
        // 发表回复
        $events->listen(Saving::class, CheckPublish::class);
        $events->listen(Created::class, [$this, 'whenPostWasCreated']);

        // 审核回复
        $events->listen(PostWasApproved::class, [$this, 'whenPostWasApproved']);

        // 隐藏/还原回复
        $events->listen(Hidden::class, [$this, 'whenPostWasHidden']);
        $events->listen(Restored::class, [$this, 'whenPostWasRestored']);

        // 删除首帖
        $events->listen(Deleted::class, [$this, 'whenPostWasDeleted']);
        $events->listen(Deleting::class, [$this, 'whenDeleteThread']);

        // 修改内容
        $events->listen(Revised::class, [$this, 'whenPostWasRevised']);

        // 喜欢帖子
        $events->listen(Serializing::class, AddPostLikeAttribute::class);
        $events->subscribe(SaveLikesToDatabase::class);

        // 添加完数据后
        $events->listen(Saved::class, [$this, 'whenPostWasSaved']);

        // @
        $events->listen(Saved::class, [$this, 'userMentions']);

        // #话题#
        $events->listen(Saved::class, [$this, 'threadTopic']);

        // 设置主题,商品关联关系
        $events->listen(Saved::class, [$this, 'postGoods']);
    }

    public function whenDeleteThread(Deleting $event){
        $actor = $event->actor;
        $actor->refreshUserLiked();
        $actor->save();
    }

    /**
     * 发送通知
     *
     * @param Created $event
     */
    public function whenPostWasCreated(Created $event)
    {
        $post = $event->post;
        $actor = $event->actor;

        if ($post->is_approved == Post::APPROVED) {
            /**
             * 如果当前用户不是主题作者，也是合法的，不是回复的回复，也不是楼中楼
             * 则通知主题作者
             */
            if (
                $post->thread->user_id != $actor->id
                && is_null($post->reply_post_id)
                && is_null($post->comment_post_id)
            ) {
                // Tag 发送通知
                $post->thread->user->notify(new Replied($actor, $post, ['notify_type' => 'notify_thread']));
            }

            /**
             * 如果被回复的用户不是当前用户，也不是主题作者，也是合法的，
             * 则通知被回复的人
             */
            if (is_null($post->comment_post_id)) {
                //2级回复
                if ($post->reply_post_id && $post->reply_user_id != $actor->id) {
                    $post->replyUser->notify(new Replied($actor, $post, ['notify_type' => 'notify_reply_post']));
                }
            } else {
                //2级回复评论
                if ($post->reply_post_id && $post->comment_user_id != $actor->id) {
                    $post->commentUser->notify(new Replied($actor, $post, ['notify_type' => 'notify_comment_post']));
                }
            }
        }
    }

    /**
     * @param Saved $event
     */
    public function whenPostWasSaved(Saved $event)
    {
        $post = $event->post;
        $thread = $post->thread;

        // 刷新主题回复数、最后一条回复
        if ($thread && $thread->exists) {
            $thread->refreshPostCount();
            $thread->refreshLastPost();
            $thread->save();
        }

        // 刷新被回复数
        if ($replyId = $post->reply_post_id) {
            /** @var Post $replyPost */
            $replyPost = Post::query()->find($replyId);
            $replyPost->timestamps = false;
            $replyPost->refreshReplyCount();
            $replyPost->save();
        }
    }

    /**
     * @param PostWasApproved $event
     */
    public function whenPostWasApproved(PostWasApproved $event)
    {
        $post = $event->post;

        if ($post->is_approved === Post::APPROVED) {
            // 审核通过时，清除记录的敏感词
            PostMod::query()->where('post_id', $post->id)->delete();

            $action = 'approve';

            $this->bus->dispatch(
                new Saved($post, $post->user, $event->data)
            );

        } elseif ($post->is_approved === Post::IGNORED) {
            $action = 'ignore';
        } else {
            $action = 'disapprove';
        }

        // 通知
        $this->postNotices($post, $event->actor, 'isApproved', $event->data['message'] ?? '');

        // 日志
        UserActionLogs::writeLog($event->actor, $post, $action, $event->data['message'] ?? '');
    }

    /**
     * 隐藏回复时
     *
     * @param Hidden $event
     */
    public function whenPostWasHidden(Hidden $event)
    {
        // 通知
        $this->postNotices($event->post, $event->actor, 'isDeleted', $event->data['message'] ?? '');

        // 日志
        UserActionLogs::writeLog($event->actor, $event->post, 'hide', $event->data['message'] ?? '');
    }

    /**
     * 还原回复时
     *
     * @param Restored $event
     */
    public function whenPostWasRestored(Restored $event)
    {
        // 日志
        UserActionLogs::writeLog($event->actor, $event->post, 'restore', $event->data['message'] ?? '');
    }

    /**
     * TODO: 删除附件
     * 如果删除的是首帖，同时删除主题及主题下所有回复
     *
     * @param Deleted $event
     */
    public function whenPostWasDeleted(Deleted $event)
    {
        if ($event->post->is_first) {
            Thread::query()->where('id', $event->post->thread_id)->delete();

            Post::query()->where('thread_id', $event->post->thread_id)->delete();
        }
    }

    /**
     * 修改内容时，记录操作
     *
     * @param Revised $event
     */
    public function whenPostWasRevised(Revised $event)
    {
        UserActionLogs::writeLog(
            $event->actor,
            $event->post,
            'revise',
            $event->actor->username . ' 修改了内容'
        );

        if ($event->post->user && $event->post->user->id != $event->actor->id) {
            $build = [
                'message' => $event->content,
                'post' => $event->post,
                'notify_type' => Post::NOTIFY_EDIT_CONTENT_TYPE,
            ];

            // Tag 发送通知
            $event->post->user->notify(new System(PostMessage::class, $event->actor, $build));
        }
    }

    /**
     * @param Saved $event
     */
    public function userMentions(Saved $event)
    {
        $post = $event->post;
        //是否草稿
        if($post->thread->is_draft == 1){
            return;
        }

        if (isset($event->data['attributes']['isLiked'])){
            return;
        }

        // 新建 或者 修改了是否合法字段 并且 合法时，发送 @ 通知
        if ($post->thread->is_draft == 0 || ($post->wasRecentlyCreated || $post->wasChanged('is_approved')) && $post->is_approved === Post::APPROVED) {
            $this->sendRelated($event->post, $event->post->user);
        }
    }

    /**
     * 解析话题、创建话题、存储话题主题关系、修改话题主题数/阅读数
     *
     * @param Saved $event
     */
    public function threadTopic(Saved $event)
    {
        $inputData = $event->data;
        if(!isset($inputData['attributes']['isLiked'])){
            ThreadTopic::setThreadTopic($event->post);
        }
    }

    /**
     * 设置商品帖的关联关系
     *
     * @param Saved $event
     * @throws Exception
     */
    public function postGoods(Saved $event)
    {
        $post = $event->post;
        if ($post->is_first && $post->thread->type === Thread::TYPE_OF_GOODS) {
            if (! Arr::has($event->data, 'attributes.post_goods_id')) {
                return;
            }

            $goodsId = (int) Arr::get($event->data, 'attributes.post_goods_id');

            $isDraft = (int) Arr::get($event->data, 'attributes.is_draft');
            if ($isDraft && !$goodsId) {
                return;
            }

            /**
             * 每个商品绑定一个 Post
             *
             * @var PostGoods $goods
             */
            $goods = PostGoods::query()->where('post_id', $post->id)->first();
            if (! empty($goods)) {
                if ($goods->id != $goodsId) {
                    $goods->delete();
                } else {
                    return;
                }
            }

            /** @var PostGoods $postGoods */
            $postGoods = PostGoods::query()->where('id', $goodsId)->where('post_id', 0)->whereNull('deleted_at')->first();
            if ($postGoods) {
                $postGoods->post_id = $post->id;
                $postGoods->save();
            } else {
                throw new Exception('post_goods_illegal');
            }
        }
    }
}
