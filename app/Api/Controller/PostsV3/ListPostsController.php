<?php

namespace App\Api\Controller\PostsV3;

use App\Api\Controller\ThreadsV3\ThreadHelper;
use App\Api\Serializer\AttachmentSerializer;
use App\Api\Serializer\PostSerializer;
use App\Common\ResponseCode;
use App\Formatter\Formatter;
use App\Models\Attachment;
use App\Models\Group;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserWalletLog;
use App\Providers\PostServiceProvider;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ListPostsController extends DzqController
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

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $filters = $this->inPut('filter') ?: [];
        $threadId = Arr::get($filters, 'thread');
        // 只有管理员能查看所有回复，暂时兼容管理后台
        if (!$threadId && !$this->user->isAdmin()) {
            return false;
        }

        $thread = Thread::query()
            ->where(['id' => $threadId])
            ->whereNull('deleted_at')
            ->first();
        if (!$thread) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }

        return $userRepo->canViewThreadDetail($this->user, $thread);
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
                'thread:id,type,category_id',
                'user:id,username,nickname,avatar,realname',
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
            ->where('posts.is_comment', false);

        if ($sort) {
            $field = ltrim(Str::snake($sort), '-');
            $query->orderBy($field, Str::startsWith($sort, '-') ? 'desc' : 'asc');
        }

        // 主题
        $threadId = Arr::get($filters, 'thread');
        if ($threadId) {
            $query->where('posts.thread_id', $threadId);
        }

        $user = $this->user;
        //如果是审核状态只能自己看到
        if (!$user->isAdmin()) {
            //如果是游客
            if ($user->isGuest()){
                $query->where('posts.is_approved', Post::APPROVED_YES);
            } else {
                $notUser = Post::query()
                    ->where('user_id','<>',$user->id)
                    ->where('is_approved','<>', Post::APPROVED_YES)
                    ->where(['is_first' => false , 'thread_id' => $threadId])
                    ->get(['id'])
                    ->pluck('id')
                    ->toArray();
                if ($notUser) $query->whereNotIn('posts.id',$notUser);
            }
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
                'thread:id,type,category_id',
                'user:id,nickname,avatar,realname',
                'user.groups:id,name,is_display',
                'commentUser:id,nickname,avatar,realname',
                'replyUser:id,nickname,avatar,realname',
                'images',
            ])
            ->from('posts', 'a')
            ->whereRaw('('.$subSql.') < ?', [3])
            ->whereIn('reply_post_id', $postIds)
            ->whereNull('deleted_at')
            ->where('is_first', false)
            ->where('is_comment', true)
            ->orderBy('updated_at', 'desc')
            ->get();
//            ->map(function (Post $post) {
//                $content = Str::of($post->content);
//                if ($content->length() > Post::SUMMARY_LENGTH) {
//                    $post->content = $content->substr(0, Post::SUMMARY_LENGTH)->finish(Post::SUMMARY_END_WITH);
//                }
//
//                return $post;
//            });

        $posts = array_map(function ($post) use ($allLastThreeComments) {
            $twoPosts =  $allLastThreeComments->where('reply_post_id', $post['id'])->take(3)->values();
            $lastThreeComments = [];
            //触发审核只有管理员和自己能看到
            foreach ($twoPosts as $posts) {
                if ($posts['is_approved'] != Post::APPROVED && $this->user->id != $posts['user_id'] && !$this->user->isAdmin()) {
                    continue;
                }
                $lastThreeComments[] =  $this->getPost($posts, false);
            }
            $post['lastThreeComments'] = $lastThreeComments;
            return $post;
        }, $posts);

        return $posts;
    }

    protected function getPost(Post $post, bool $getRedPacketAmount)
    {

        $userRepo = app(UserRepository::class);

        if ($getRedPacketAmount && !$this->user->isGuest()) {
            $auditCount = Post::query()
                ->where('reply_post_id',$post['id'])
                ->where('is_approved' , '<>' , Post::APPROVED);
            if (!$this->user->isAdmin()) {
                $auditCount->where('user_id',$this->user->id);
            }
            $auditCount = $auditCount->count();
            $post['reply_count'] = intval($post['reply_count'] + $auditCount);
        }

        $data = [
            'id' => $post['id'],
            'userId' => $post['user_id'],
            'threadId' => $post['thread_id'],
            'replyPostId' => $post['reply_post_id'],
            'replyUserId' => $post['reply_user_id'],
            'commentPostId' => $post['comment_post_id'],
            'commentUserId' => $post['comment_user_id'],
//            'content' => str_replace(['<t><p>', '</p></t>'], ['', ''],$post['content']),
            'replyCount' => $post['reply_count'],
            'likeCount' => $post['like_count'],
            'createdAt' => optional($post->created_at)->format('Y-m-d H:i:s'),
            'updatedAt' => optional($post->updated_at)->format('Y-m-d H:i:s'),
            'isFirst' => $post['is_first'],
            'isComment' => $post['is_comment'],
            'isApproved' => $post['is_approved'],
            'rewards' => floatval(sprintf('%.2f', $post->getPostReward(UserWalletLog::TYPE_INCOME_THREAD_REWARD))),
            'canApprove' => $this->user->isAdmin(),
            'canDelete' => $this->user->isAdmin(),
            'canHide' => $userRepo->canHidePost($this->user, $post),
            'canLike' => $userRepo->canLikePosts($this->user),
            'user' => $this->getUser($post->user),
            'images' => $post->images->map(function (Attachment $image) {
                return $this->attachmentSerializer->getDefaultAttributes($image);
            }),
            'likeState' => $post->likeState,
            'summaryText' => str_replace(['<t><p>', '</p></t>'], ['', ''],$post->summary_text),
        ];
        if($post->thread->type != Thread::TYPE_OF_ALL){     //老数据
            $data['content']  =  app()->make(Formatter::class)->render($post['content']);
        }else{
//            $content = str_replace(['<r>', '</r>', '<t>', '</t>'], ['', '', '', ''], $post['content']);
//            list($searches, $replaces) = ThreadHelper::getThreadSearchReplace($content);
//            $data['content'] = str_replace($searches, $replaces, $content);
            $data['content'] = $post['content'];
        }

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

        $userData = $user->toArray();
        $data = array_merge($userData, [
            'isReal'   => !empty($user->realname)
        ]);
        if ($user->relationLoaded('groups')) {
            $groupInfos = $user->groups->toArray();
            $data['groups'] =  [
                    'id' => $groupInfos[0]['id'],
                    'name' => $groupInfos[0]['name'],
                    'isDisplay' => $groupInfos[0]['is_display'],
                ];
        }

        return $data;
    }
}
