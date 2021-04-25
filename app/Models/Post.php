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

namespace App\Models;

use App\Common\CacheKey;
use App\Events\Post\Hidden;
use App\Events\Post\Restored;
use App\Events\Post\Revised;
use App\Models\UserWalletLog;
use App\Formatter\Formatter;
use Carbon\Carbon;
use DateTime;
use Discuz\Base\DzqModel;
use Discuz\Database\ScopeVisibilityTrait;
use Discuz\Foundation\EventGeneratorTrait;
use Discuz\SpecialChar\SpecialCharServer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $user_id
 * @property int $thread_id
 * @property int $reply_post_id
 * @property int $reply_user_id
 * @property int $comment_post_id
 * @property int $comment_user_id
 * @property string $summary
 * @property string $summary_text
 * @property string $content
 * @property string $ip
 * @property int $port
 * @property int $reply_count
 * @property int $like_count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property int $deleted_user_id
 * @property bool $is_first
 * @property bool $is_comment
 * @property bool $is_approved
 * @property Collection $images
 * @property Collection $attachments
 * @property Thread $thread
 * @property User $user
 * @property User $replyUser
 * @property User $commentUser
 * @property User $deletedUser
 * @property PostMod $stopWords
 * @property Post $replyPost
 * @property Post $commentPost
 * @property string $parsedContent
 * @package App\Models
 */
class Post extends DzqModel
{
    use EventGeneratorTrait;
    use ScopeVisibilityTrait;

    /**
     * 通知 Post 操作方式
     */
    const NOTIFY_EDIT_CONTENT_TYPE = 'edit_content';    // 内容修改
    const NOTIFY_UNAPPROVED_TYPE   = 'unapproved';      // 内容不合法/内容忽略
    const NOTIFY_APPROVED_TYPE     = 'approved';        // 内容合法
    const NOTIFY_ESSENCE_TYPE      = 'essence';         // 内容加精
    const NOTIFY_STICKY_TYPE       = 'sticky';          // 内容置顶
    const NOTIFY_DELETE_TYPE       = 'delete';          // 内容删除

    /**
     * 摘要长度
     */
    const SUMMARY_LENGTH = 80;

    /**
     * 摘要结尾
     */
    const SUMMARY_END_WITH = '...';

    /**
     * 通知内容展示长度(字)
     */
    const NOTICE_LENGTH = 80;

    const UNAPPROVED = 0;

    const APPROVED = 1;

    const IGNORED = 2;


    const FIRST_YES = 1;
    const FIRST_NO = 0;

    const COMMENT_YES = 1;
    const COMMENT_NO = 0;

    const APPROVED_YES = 1;
    const APPROVED_NO = 0;
    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'reply_count' => 'integer',
        'like_count' => 'integer',
        'is_first' => 'boolean',
        'is_comment' => 'boolean',
    ];

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Post 操作方式的变量音译
     * 修改/不通过/通过/精华/置顶/删除
     *
     * @var string[]
     */
    protected static $notifyType = [
        'edit_content' => '修改',
        'unapproved'   => '不通过',
        'approved'     => '通过',
        'essence'      => '精华',
        'sticky'       => '置顶',
        'delete'       => '删除',
    ];

    /**
     * The user for which the state relationship should be loaded.
     *
     * @var User
     */
    protected static $stateUser;

    /**
     * The text formatter instance.
     *
     * @var Formatter
     */
    protected static $formatter;

    /**
     * 根据 值/类型 获取对应值
     *
     * @param $string
     * @return string
     */
    public static function enumNotifyType($string): string
    {
        $arr = static::$notifyType;

        return array_key_exists($string, $arr) ? $arr[$string] : '未知';
    }

    /**
     * datetime 时间转换
     *
     * @param $timeAt
     * @return string
     */
    public function formatDate($timeAt)
    {
        return $this->{$timeAt}->format(DateTime::RFC3339);
    }

    /**
     * 帖子摘要
     *
     * @return string
     */
    public function getSummaryAttribute()
    {
        $content = Str::of($this->content ?: '');
        if ($content->length() > self::SUMMARY_LENGTH) {
            $subContent = $content->substr(0, self::SUMMARY_LENGTH);
            if(stristr($subContent,'http')){
                $content = Str::of(strip_tags($this->formatContent()));
            }
            $content = static::$formatter->parse(
                $content->substr(0, self::SUMMARY_LENGTH)->finish(self::SUMMARY_END_WITH)
            );
            $content = static::$formatter->render($content);
        } else {
            $content = $this->formatContent();
        }

        return str_replace('<br>', '', $content);
    }

    /**
     * 帖子纯文本摘要
     *
     * @return string
     */
    public function getSummaryTextAttribute()
    {
       $content = Str::of($this->content ?: '');

        if ($content->length() > self::SUMMARY_LENGTH) {

            $content = static::$formatter->parse(
                $content->substr(0, self::SUMMARY_LENGTH)->finish(self::SUMMARY_END_WITH)
            );
            $content = static::$formatter->render($content);
        }
        return str_replace('<br>', '', $content);
    }

    /**
     * Unparse the parsed content.
     *
     * @param string $value
     * @return string
     */
    public function getContentAttribute($value)
    {
        return html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get the parsed/raw content.
     *
     * @return string
     */
    public function getParsedContentAttribute()
    {
        return $this->attributes['content'];
    }

    /**
     * Parse the content before it is saved to the database.
     *
     * @param string $value
     */
    public function setContentAttribute($value)
    {
        if (blank($value) && $this->is_first) {
            $defaultContent = [
                Thread::TYPE_OF_VIDEO => trans('post.default_content.video'),
                Thread::TYPE_OF_IMAGE => trans('post.default_content.image'),
                Thread::TYPE_OF_AUDIO => trans('post.default_content.audio'),
                Thread::TYPE_OF_GOODS => trans('post.default_content.goods'),
            ];

            $value = $defaultContent[$this->thread->type] ?? '';
        }

        $this->attributes['content'] = $value ? static::$formatter->parse($value, $this) : null;
    }

    /**
     * Set the parsed/raw content.
     *
     * @param string $value
     */
    public function setParsedContentAttribute($value)
    {
        $this->attributes['content'] = $value;
    }

    /**
     * Get the content rendered as HTML.
     *
     * @return string
     */
    public function formatContent()
    {
        if (empty($this->attributes['content'])) {
            return $this->attributes['content'];
        }
        empty(static::$formatter) && Post::setFormatter(app()->make(Formatter::class));
        return static::$formatter->render($this->attributes['content']);
    }

    /**
     * 获取 Content & firstContent
     *
     * @param $substr
     * @param bool $parse
     * @return string[]
     */
    public function getSummaryContent($substr, $parse = false)
    {
        $special = app(SpecialCharServer::class);

        $build = [
            'content' => '',
            'first_content' => '',
        ];

        $this->content = $substr ? Str::of($this->content)->substr(0, $substr) : $this->content;
        if ($parse) {
            // 原文
            $content = $this->content;
        } else {
            $content = $this->formatContent();
        }

        /**
         * 判断是否是楼中楼的回复
         */
        if ($this->reply_post_id) {
            // Do something
            // TODO comment_post_id 评论回复 id
        } else {
            /**
             * 判断长文点赞通知内容为标题
             */
            if ($this->thread->type === Thread::TYPE_OF_LONG) {
                $firstContent = $this->thread->getContentByType(self::NOTICE_LENGTH, $parse);
            } else {
                // 如果是首帖 firstContent === content 内容一样
                if ($this->is_first) {
                    $firstContent = $content;
                } else {
                    $firstContent = $this->thread->getContentByType(self::NOTICE_LENGTH, $parse);
                }
            }
        }

        $build['content'] = $content;
        $build['first_content'] = $firstContent ?? $special->purify($this->thread->getContentByType(Thread::CONTENT_LENGTH, $parse));

        return $build;
    }

    /**
     * Create a new instance in reply to a thread.
     *
     * @param int $threadId
     * @param string $content
     * @param int $userId
     * @param string $ip
     * @param int $port
     * @param int $replyPostId
     * @param int $replyUserId
     * @param int $commentPostId
     * @param int $commentUserId
     * @param int $isFirst
     * @param int $isComment
     * @return static
     */
    public static function reply( $threadId, $content, $userId, $ip, $port, $replyPostId, $replyUserId, $commentPostId, $commentUserId, $isFirst, $isComment, Post $post=null)
    {
        if (!$post->id)
        $post = new static;

        $post->created_at = Carbon::now();
        $post->thread_id = $threadId;
        $post->user_id = $userId;
        $post->ip = $ip;
        $post->port = $port;
        $post->reply_post_id = $replyPostId;
        $post->reply_user_id = $replyUserId;
        $post->comment_post_id = $commentPostId;
        $post->comment_user_id = $commentUserId;
        $post->is_first = $isFirst;
        $post->is_comment = $isComment;

        // Set content last, as the parsing may rely on other post attributes.
        $post->content = $content;

        return $post;
    }

    /**
     * Revise the post's content.
     *
     * @param string $content
     * @param User $actor
     * @return $this
     */
    public function revise($content, User $actor)
    {
        if ($this->content !== $content) {
            $rawContent = $this->content;

            $this->content = $content;

            $this->raise(new Revised($this, $rawContent, $actor));
        }

        return $this;
    }

    /**
     * Hide the post.
     *
     * @param User $actor
     * @param array $options
     * @return $this
     */
    public function hide(User $actor, $options = [])
    {
        if (! $this->deleted_at) {
            $this->deleted_at = Carbon::now();
            $this->deleted_user_id = $actor->id;

            $this->raise(new Hidden($this, $actor, $options));
        }

        return $this;
    }

    /**
     * Restore the post.
     *
     * @param User $actor
     * @param array $options
     * @return $this
     */
    public function restore(User $actor, $options = [])
    {
        if ($this->deleted_at !== null) {
            $this->deleted_at = null;
            $this->deleted_user_id = null;

            $this->raise(new Restored($this, $actor, $options));
        }

        return $this;
    }

    /**
     * Refresh the thread's post count.
     *
     * @return $this
     */
    public function refreshLikeCount()
    {
        $this->like_count = $this->likedUsers()->count();

        return $this;
    }

    /**
     * Refresh the post's reply count.
     *
     * @return $this
     */
    public function refreshReplyCount()
    {
        $this->reply_count = $this->newQuery()
            ->where('reply_post_id', $this->id)
            ->where('is_approved', Post::APPROVED)
            ->whereNull('deleted_at')
            ->whereNotNull('user_id')
            ->count();

        return $this;
    }

    /**
     * Define the relationship with the post's thread.
     *
     * @return BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Define the relationship with the post's author.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define the relationship with the post's reply user.
     *
     * @return BelongsTo
     */
    public function replyUser()
    {
        return $this->belongsTo(User::class, 'reply_user_id');
    }

    /**
     * Define the relationship with the post's comment user.
     *
     * @return BelongsTo
     */
    public function commentUser()
    {
        return $this->belongsTo(User::class, 'comment_user_id');
    }

    /**
     * Define the relationship with the post's content post.
     *
     * @return BelongsTo
     */
    public function replyPost()
    {
        return $this->belongsTo(Post::class, 'reply_post_id');
    }

    /**
     * Define the relationship with the post's content post.
     *
     * @return BelongsTo
     */
    public function commentPost()
    {
        return $this->belongsTo(Post::class, 'comment_post_id');
    }

    /**
     * Define the relationship with the user who hid the post.
     *
     * @return BelongsTo
     */
    public function deletedUser()
    {
        return $this->belongsTo(User::class, 'deleted_user_id');
    }

    /**
     * Define the relationship with the thread's operation Log.
     */
    public function logs()
    {
        return $this->morphMany(UserActionLogs::class, 'log_able');
    }

    /**
     * Define the relationship with the post's liked users.
     *
     * @return BelongsToMany
     */
    public function likedUsers()
    {
        return $this->belongsToMany(User::class)
            ->orderBy('post_user.created_at', 'desc')
            ->withPivot('created_at');
    }

    /**
     * Define the relationship with the post's stop words.
     *
     * @return HasOne
     */
    public function stopWords()
    {
        return $this->hasOne(PostMod::class);
    }

    /**
     * Define the relationship with the post's images.
     *
     * @return HasMany
     */
    public function images()
    {
        return $this->hasMany(Attachment::class, 'type_id')->where('type', Attachment::TYPE_OF_IMAGE)->orderBy('order');
    }

    /**
     * Define the relationship with the post's attachments.
     *
     * @return HasMany
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'type_id')->where('type', Attachment::TYPE_OF_FILE)->orderBy('order');
    }

    /**
     * Define the relationship with the post's like state for a particular user.
     *
     * @param User|null $user
     * @return HasOne
     */
    public function likeState(User $user = null)
    {
        $user = $user ?: static::$stateUser;

        return $this->hasOne(PostUser::class)->where('user_id', $user ? $user->id : null);
    }

    public function mentionUsers()
    {
        return $this->belongsToMany(User::class, 'post_mentions_user', 'post_id', 'mentions_user_id');
    }

    /**
     * @return HasOne
     */
    public function postGoods()
    {
        return $this->hasOne(PostGoods::class);
    }

    /**
     * Set the user for which the state relationship should be loaded.
     *
     * @param User $user
     */
    public static function setStateUser(User $user)
    {
        static::$stateUser = $user;
    }

    /**
     * Get the text formatter instance.
     *
     * @return Formatter
     */
    public static function getFormatter()
    {
        return static::$formatter;
    }

    /**
     * Set the text formatter instance.
     *
     * @param Formatter $formatter
     */
    public static function setFormatter(Formatter $formatter)
    {
        static::$formatter = $formatter;
    }

    public function save(array $options = [])
    {
        $this->removePostCache();
        return parent::save($options); // TODO: Change the autogenerated stub
    }

    public function delete()
    {
        $this->removePostCache();
        return parent::delete(); // TODO: Change the autogenerated stub
    }

    public function update(array $attributes = [], array $options = [])
    {
        $this->removePostCache();
        return parent::update($attributes, $options); // TODO: Change the autogenerated stub
    }

    private function removePostCache()
    {
        $threadId = $this->thread_id;
        $cacheKey0 = CacheKey::POST_RESOURCE_BY_ID . '0' . $threadId;
        $cacheKey1 = CacheKey::POST_RESOURCE_BY_ID . '1' . $threadId;
        $cache = app('cache');
        $cache->forget(CacheKey::LIST_V2_THREADS);
        $f1 = $cache->forget($cacheKey0);
        $f2 = $cache->forget($cacheKey1);
        return $f1 || $f2;
    }

    public function getPostReward()
    {
        $this->removePostCache();
        $thread = ($this->relationLoaded('thread') && array_key_exists('type', $this->thread->attributes))
            ? $this->thread
            : Thread::query()->select(['id', 'type'])->where('id', $this->thread_id)->first();
        $this->rewards = 0;
        if($thread->type == Thread::TYPE_OF_QUESTION){
            $this->rewards = UserWalletLog::query()
                ->where(['post_id' => $this->id, 'thread_id' => $this->thread_id])
                ->sum('change_available_amount');
        }
        return $this->rewards;
    }


    public function getPosts($threadIds)
    {
        return self::query()
            ->whereIn('thread_id', $threadIds)
            ->whereNull('reply_user_id')
            ->whereNull('deleted_at')
            ->where([
                'is_first' => self::FIRST_YES,
                'is_comment' => self::COMMENT_NO,
                'is_approved' => self::APPROVED_YES
            ])
            ->get()->toArray();
    }

    public function getContentSummary($post, &$postId = false)
    {
//        $post = self::query()->where(['thread_id' => $threadId, 'is_first' => self::FIRST_YES])->first();
        if (empty($post)) {
            return '';
        } else {
            $content = strip_tags($post['content']);
            if (mb_strlen($content) > self::SUMMARY_LENGTH) {
                $content = Str::substr($content, 0, self::SUMMARY_LENGTH) . self::SUMMARY_END_WITH;
            }
            $postId == true && $postId = $post['id'];
            return $content;
        }
    }
}
