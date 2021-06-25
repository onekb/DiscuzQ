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

namespace App\Commands\Post;

use App\Censor\Censor;
use App\Events\Post\Created;
use App\Events\Post\Saved;
use App\Events\Post\Saving;
use App\Models\Post;
use App\Models\PostMod;
use App\Models\Thread;
use App\Models\User;
use App\Repositories\ThreadRepository;
use App\Validators\PostValidator;
use Carbon\Carbon;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Foundation\EventsDispatchTrait;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class CreatePost
{
    use AssertPermissionTrait;
    use EventsDispatchTrait;

    const LIMIT_RED_PACKET_TIME = 30;
    /**
     * The id of the thread.
     *
     * @var int
     */
    public $threadId;

    /**
     * The id of the post waiting to be replied.
     *
     * @var int
     */
    public $replyPostId;

    /**
     * The id of the post waiting to be replied.
     *
     * @var int
     */
    public $replyUserId;

    /**
     * The id of the post waiting to be replied.
     *
     * @var int
     */
    public $commentPostId;

    /**
     * The id of the post waiting to be replied.
     *
     * @var int
     */
    public $commentUserId;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes of the new thread.
     *
     * @var array
     */
    public $data;

    /**
     * The current ip address of the actor.
     *
     * @var string
     */
    public $ip;

    /**
     * The current port of the actor.
     *
     * @var int
     */
    public $port;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var null
     */
    protected $isFirst;

    /**
     * @param int $threadId
     * @param User $actor
     * @param array $data
     * @param string $ip
     * @param int $port
     * @param null $isFirst
     */
    public function __construct($threadId, User $actor, array $data, $ip, $port, $isFirst = null)
    {
        $this->threadId = $threadId;
        $this->replyPostId = Arr::get($data, 'attributes.replyId', null);
        $this->commentPostId = Arr::get($data, 'attributes.commentPostId', null);
        $this->actor = $actor;
        $this->data = $data;
        $this->ip = $ip;
        $this->port = $port;
        $this->isFirst = $isFirst;
    }

    /**
     * @param Dispatcher $events
     * @param ThreadRepository $threads
     * @param PostValidator $validator
     * @param Censor $censor
     * @param Post $post
     * @return Post
     * @throws PermissionDeniedException
     * @throws Exception
     * @throws ValidationException
     * @throws GuzzleException
     */
    public function handle(Dispatcher $events, ThreadRepository $threads, PostValidator $validator, Censor $censor, Post $post)
    {
        $cache = app('cache');
        $this->events = $events;

        $thread = $threads->findOrFail($this->threadId);
        $attributes = Arr::get($this->data, 'attributes', '');

        if($thread->is_red_packet != Thread::NOT_HAVE_RED_PACKET && (Carbon::now()->timestamp - $thread->created_at->timestamp > 30)){
            $cacheKey = 'thread_red_packet_'.md5($this->actor->id);
            $red_cache = $cache->get($cacheKey);
            if($red_cache){
                $cache->put($cacheKey, true, self::LIMIT_RED_PACKET_TIME);
                throw new Exception(trans('user.do_frequent'));
            }
        }

        $isFirst = is_null($this->isFirst) ? empty($thread->post_count):$this->isFirst;

        if ($isFirst && ($firstPost = $thread->firstPost)) {
            $post = $firstPost;
        }

        if (!$isFirst) {
            // 非首帖，检查是否有权回复
            $this->assertCan($this->actor, 'reply', $thread);

            // 回复中回复，确保回复在同一主题下
            if (! empty($this->commentPostId)) {
                /** @var Post $comment */
                $comment = $post->newQuery()
                    ->where('id', $this->commentPostId)
                    ->where('thread_id', $thread->id)
                    ->first(['user_id', 'reply_post_id']);

                $this->commentUserId = $comment->user_id;
                $this->replyPostId = $comment->reply_post_id;

                if (! $this->commentUserId) {
                    throw new ModelNotFoundException;
                }
            }

            // 回复他人
            if (! empty($this->replyPostId)) {
                // 被回复人，确保回复在同一主题下
                $this->replyUserId = $post->newQuery()
                    ->where('id', $this->replyPostId)
                    ->where('thread_id', $thread->id)
                    ->value('user_id');

                if (! $this->replyUserId) {
                    throw new ModelNotFoundException;
                }
            }
        }

        $post = $post->reply(
            $thread->id,
            trim(Arr::get($this->data, 'attributes.content')),
            $this->actor->id,
            $this->ip,
            $this->port,
            $this->replyPostId,
            $this->replyUserId,
            $this->commentPostId,
            $this->commentUserId,
            $isFirst,
            (bool) Arr::get($this->data, 'attributes.isComment'),
            $post
        );

        // first post 的内容，已经在 CreateThread 中检测过了
        if (!$isFirst) {
            $post->content = $censor->checkText($post->content);
        }

        $content = $post->content;
        $postContent = trim(Arr::get($this->data, 'attributes.content'));

        if (mb_strlen($postContent) > 49998) {
            $dataString = json_encode(Arr::get($this->data, 'attributes'));
            app('log')->info('用户:' . $this->actor->id . '，帖子post-content字数超过限制，帖子thread_id为：' . $post->thread_id . '，内容为：' . $postContent . '，data数据为：' . $dataString);
            throw new \Exception('字数超出限制');
        }

        if ($isFirst && !Arr::get($this->data, 'attributes.is_draft')) {
            if (empty($postContent) && 
            ($attributes['type'] == Thread::TYPE_OF_TEXT || 
            $attributes['type'] == Thread::TYPE_OF_LONG || 
            $attributes['type'] == Thread::TYPE_OF_QUESTION)) {
                $dataString = json_encode(Arr::get($this->data, 'attributes'));
                app('log')->info('用户:' . $this->actor->id . '，帖子post-content字数为空，帖子thread_id为：' . $post->thread_id . '，内容为：' . $postContent . '，data数据为：' . $dataString);
                throw new \Exception('内容不能为空');
            }
        }

        if(!$isFirst && empty($postContent)){
            $dataString = json_encode(Arr::get($this->data, 'attributes'));
            app('log')->info('用户:' . $this->actor->id . '，帖子post-content字数为空，帖子thread_id为：' . $post->thread_id . '，内容为：' . $postContent . '，data数据为：' . $dataString);
            throw new \Exception('内容不能为空');
        }

        // 存在审核敏感词时，将回复放入待审核
        if ($censor->isMod) {
            $post->is_approved = Post::UNAPPROVED;
        } else {
            $post->is_approved = Post::APPROVED;
        }

        $post->raise(new Created($post, $this->actor, $this->data));

        $this->events->dispatch(
            new Saving($post, $this->actor, $this->data)
        );

        $post->save();

        //这里判断是否为红包贴，如果是红包贴则限制用户回帖时间
        if($thread->is_red_packet != Thread::NOT_HAVE_RED_PACKET && (Carbon::now()->timestamp - $thread->created_at->timestamp > 30)){
            $cacheKey = 'thread_red_packet_'.md5($this->actor->id);
            $cache->put($cacheKey, true, self::LIMIT_RED_PACKET_TIME);
        }

        // 记录触发的审核词
        if (!$isDraft = Arr::get($this->data, 'attributes.is_draft')) {
            if ($post->is_approved === Post::UNAPPROVED && $censor->wordMod) {
                $stopWords = new PostMod;
                $stopWords->stop_word = implode(',', array_unique($censor->wordMod));

                $post->stopWords()->save($stopWords);
            }
        }

        $post->raise(new Saved($post, $this->actor, $this->data));

        // TODO: 通知相关用户，在给定的整个持续时间内，每位用户只能收到一个通知
        // $this->notifications->onePerUser(function () use ($post, $actor) {
        $this->dispatchEventsFor($post, $this->actor);
        // });

        $post->rewards = floatval(sprintf('%.2f', $post->getPostReward()));
        $post->content != $content && $post->content = $content;
        return $post;
    }
}
