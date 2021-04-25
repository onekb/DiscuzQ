<?php

namespace App\Api\Controller\Posts;

use App\Api\Serializer\AttachmentSerializer;
use App\Api\Serializer\PostSerializer;
use App\Common\ResponseCode;
use App\Models\Attachment;
use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use App\Providers\PostServiceProvider;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ListPostsV2Controller extends DzqController
{
    protected $postSerializer;

    protected $attachmentSerializer;

    protected $gate;

    public $providers = [
        PostServiceProvider::class,
    ];

    public function __construct(
        PostSerializer $postSerializer,
        AttachmentSerializer $attachmentSerializer,
        Gate $gate
    ) {
        $this->postSerializer = $postSerializer;
        $this->attachmentSerializer = $attachmentSerializer;
        $this->gate = $gate;
    }

    public function main()
    {
        $this->attachmentSerializer->setRequest($this->request);

        /** @var User $user */
        $user = $this->user;
        $this->gate = $this->gate->forUser($user);

        $filters = $this->inPut('filter') ?: [];
        $page = $this->inPut('page') ?: 1;
        $perPage = $this->inPut('perPage') ?: 5;
        $sort = $this->inPut('sort');

        $posts = $this->search($filters, $perPage, $page, $sort);
        $posts['pageData'] = $posts['pageData']->map(function ($post) {
            return $this->getPost($post, true);
        })->sortByDesc('rewards')->values()->toArray();
        $posts['pageData'] = $this->getLastThreeComments($posts['pageData']);

        $this->outPut(ResponseCode::SUCCESS, '', $posts);
    }

    protected function search(array $filters, int $perPage, int $page, $sort)
    {
        Post::setStateUser($this->user);
        $query = Post::query()
            ->with([
                'thread:id,type',
                'user:id,username,avatar,realname',
                'user.groups:id,name,is_display',
                'images',
                'likeState',
            ])
            ->select('posts.*');

        $this->applyFilters($query, $filters, $sort);

        return $this->pagination($page, $perPage, $query, false);
    }

    protected function applyFilters(Builder $query, array $filters, $sort)
    {
        $query->where('posts.is_first', false)
            ->whereNull('posts.deleted_at')
            ->where('posts.is_comment', false)
            ->where('posts.is_approved', Post::APPROVED);

        if ($sort) {
            $field = ltrim(Str::snake($sort), '-');
            $query->orderBy($field, Str::startsWith($sort, '-') ? 'desc' : 'asc');
        }

        // 主题
        if ($threadId = Arr::get($filters, 'thread')) {
            $query->where('posts.thread_id', $threadId);
        }

        $query->orderBy('created_at');
    }

    protected function getLastThreeComments($posts)
    {
        $postIds = array_column($posts, 'id');

        $tablePrefix = config('database.connections.mysql.prefix');
        $subSql = Post::query()
            ->selectRaw('count(*)')
            ->whereRaw($tablePrefix.'a.`id` < `id`')
            ->whereRaw($tablePrefix.'a.`reply_post_id` = `reply_post_id`')
            ->whereRaw($tablePrefix.'a.`deleted_at` = `deleted_at`')
            ->whereRaw($tablePrefix.'a.`is_first` = `is_first`')
            ->whereRaw($tablePrefix.'a.`is_comment` = `is_comment`')
            ->whereRaw($tablePrefix.'a.`is_approved` = `is_approved`')
            ->toSql();

        $allLastThreeComments = Post::query()
            ->with([
                'thread:id,type',
                'user:id,username,avatar,realname',
                'commentUser:id,username,avatar,realname',
                'replyUser:id,username,avatar,realname',
                'images',
            ])
            ->from('posts', 'a')
            ->whereRaw('('.$subSql.') < ?', [3])
            ->whereIn('reply_post_id', $postIds)
            ->whereNull('deleted_at')
            ->where('is_first', false)
            ->where('is_comment', true)
            ->where('is_approved', Post::APPROVED)
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function (Post $post) {
                $content = Str::of($post->content);

                if ($content->length() > Post::SUMMARY_LENGTH) {
                    $post->content = $content->substr(0, Post::SUMMARY_LENGTH)->finish(Post::SUMMARY_END_WITH);
                }

                return $post;
            });

        $posts = array_map(function ($post) use ($allLastThreeComments) {
            $post['lastThreeComments'] = $allLastThreeComments->where('reply_post_id', $post['id'])->take(3)->map(function ($post) {
                return $this->getPost($post, false);
            })->values()->toArray();
            return $post;
        }, $posts);

        return $posts;
    }

    protected function getPost(Post $post, bool $getRedPacketAmount)
    {
        $data = [
            'id' => $post['id'],
            'userId' => $post['user_id'],
            'threadId' => $post['thread_id'],
            'replyPostId' => $post['reply_post_id'],
            'replyUserId' => $post['reply_user_id'],
            'commentPostId' => $post['comment_post_id'],
            'commentUserId' => $post['comment_user_id'],
            'content' => $post['content'],
            'contentHtml' => $post->formatContent(),
            'replyCount' => $post['reply_count'],
            'likeCount' => $post['like_count'],
            'createdAt' => optional($post->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => optional($post->updated_at)->format('Y-m-d H:i:s'),
            'isFirst' => $post['is_first'],
            'isComment' => $post['is_comment'],
            'isApproved' => $post['is_approved'],
            'rewards' => floatval(sprintf('%.2f', $post->getPostReward())),
            'canApprove' => $this->gate->allows('approve', $post),
            'canDelete' => $this->gate->allows('delete', $post),
            'canHide' => $this->gate->allows('hide', $post),
            'canEdit' => $this->gate->allows('edit', $post),
            'user' => $this->getUser($post->user),
            'images' => $post->images->map(function (Attachment $image) {
                return $this->attachmentSerializer->getDefaultAttributes($image);
            }),
            'likeState' => $post->likeState,
            'canLike' => $this->user->can('like', $post),
            'summary' => $post->summary,
            'summaryText' => $post->summary_text,
        ];

        if ($post->deleted_at) {
            $data['isDeleted'] = true;
            $data['deletedAt'] = $post->deleted_at->format('Y-m-d H:i:s');
        } else {
            $data['isDeleted'] = false;
        }

        if ($getRedPacketAmount) {
            $data['redPacketAmount'] = $this->postSerializer->getPostRedPacketAmount($post['id'], $post['thread_id'], $post['user_id']);
        }

        if ($post->relationLoaded('replyUser')) {
            $data['replyUser'] = $post->replyUser;
        }

        if ($likeState = $post->likeState) {
            $data['isLiked'] = true;
            $data['likedAt'] = $likeState->created_at->format('Y-m-d H:i:s');
        } else {
            $data['isLiked'] = false;
        }

        if ($post->relationLoaded('commentUser')) {
            $data['commentUser'] = $this->getUser($post->commentUser);
        }

        if ($post->relationLoaded('replyUser')) {
            $data['replyUser'] = $this->getUser($post->replyUser);
        }

        return $data;
    }

    protected function getUser(?User $user)
    {
        if (!$user) {
            return null;
        }

        $data = array_merge($user->toArray(), [
            'isReal' => !empty($post->user->realname),
        ]);
        if ($user->relationLoaded('groups')) {
            $data['groups'] = $user->groups->map(function (Group $i) {
                return [
                    'id' => $i->id,
                    'name' => $i->name,
                    'isDisplay' => $i->is_display,
                ];
            });
        }
        return $data;
    }
}
