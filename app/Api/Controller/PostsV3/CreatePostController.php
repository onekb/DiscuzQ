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
use App\Api\Controller\ThreadsV3\ThreadTrait;
use App\Api\Serializer\AttachmentSerializer;
use App\Api\Serializer\PostSerializer;
use App\Commands\Post\CreatePost;
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Formatter\Formatter;
use App\Models\Attachment;
use App\Models\GroupUser;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserWalletLog;
use App\Providers\PostServiceProvider;
use App\Repositories\UserRepository;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;

class CreatePostController extends DzqController
{
    use ThreadTrait;
    public $include = [
        'user',
        'thread',
        'images',
    ];

    public $optionalInclude = [
        'commentUser',
    ];

    use AssertPermissionTrait;

    protected $postSerializer;

    protected $attachmentSerializer;

    protected $gate;

    public $providers = [
        PostServiceProvider::class,
    ];

    public function __construct(
        PostSerializer $postSerializer,
        AttachmentSerializer $attachmentSerializer,
        Gate $gate,
        Dispatcher $bus
    )
    {
        $this->postSerializer = $postSerializer;
        $this->attachmentSerializer = $attachmentSerializer;
        $this->gate = $gate;
        $this->bus = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        if ($this->user->status == User::STATUS_NEED_FIELDS) {
            $this->outPut(ResponseCode::JUMP_TO_SIGIN_FIELDS);
        }
        if ($this->user->status == User::STATUS_MOD) {
            $this->outPut(ResponseCode::JUMP_TO_AUDIT);
        }
        $thread = Thread::query()
            ->where([
                'id' => $this->inPut('id'),
                'is_approved' => Thread::BOOL_YES,
                'is_draft' => Thread::BOOL_NO,
            ])
            ->whereNull('deleted_at')
            ->first();
        if (!$thread) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }
        return $userRepo->canViewThreadDetail($this->user, $thread->category_id) && $userRepo->canReplyThread($this->user, $thread->category_id);
    }

    public function prefixClearCache($user)
    {
        $threadId = $this->inPut('id');
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_THREADS, $threadId);
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_POSTS, $threadId);
        DzqCache::delKey(CacheKey::LIST_THREADS_V3_POST_TIME);
    }

    public function main()
    {
        $this->attachmentSerializer->setRequest($this->request);
        $user = $this->user;
        $this->gate = $this->gate->forUser($user);

        $actor = $this->user;
        $data = [
            'content' => $this->inPut('content'),
            'isComment' => $this->inPut('isComment'),
            'replyId' => $this->inPut('replyId'),
            'replyUserId' => $this->inPut('replyUserId'),
            'commentPostId' => $this->inPut('commentPostId'),
            'commentUserId' => $this->inPut('commentUserId'),
        ];
        $threadId = $this->inPut('id');
        if (empty($threadId)) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '主题id不能为空');
        }

        //针对创建评论，前端不传标签的情况，转化 \n 为  <p></p>  实现换行
        if(strpos($data['content'], "\n") !== false){
            $data['content'] = preg_replace('/(.*)(\\n)/U', "<p>$1</p>", $data['content']);
        }
        //转换 @ #
        $data['content'] = $this->renderCall($data['content']);
        $data['content'] = $this->renderTopic($data['content']);

        $content = $data['content'];

        if (empty($data['replyId'])) {
            unset($data['replyId']);
        }

        if (empty($data['commentPostId'])) {
            unset($data['commentPostId']);
        }

        $requestData = [
            "type" => "posts",
            "relationships" => [
                "thread" => [
                    "data" => [
                        "type" => "threads",
                        'id' => $this->inPut('id'),
                    ]
                ],
            ]
        ];

        if (!empty($this->inPut('attachments'))) {
            $attachments = $this->inPut('attachments');
            foreach ($attachments as $k => $val) {
                $requestData['relationships']['attachments']['data'][$k]['id'] = (string)$val['id'];
                $requestData['relationships']['attachments']['data'][$k]['type'] = $val['type'];
            }
        }

        if (empty($content) && empty($attachments)) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '缺少必传参数');
        }

        $requestData['attributes'] = $data;
        $ip = ip($this->request->getServerParams());
        $port = Arr::get($this->request->getServerParams(), 'REMOTE_PORT', 0);
        $post = $this->bus->dispatch(
            new CreatePost($threadId, $actor, $requestData, $ip, $port)
        );
        $build = $this->getPost($post, true);
        $data = $this->camelData($build);
        // 返回content要解析后的，方便前端直接展示最新的数据
        $content = str_replace(['<r>', '</r>', '<t>', '</t>'], ['', '', '', ''], $content);
        list($searches, $replaces) = ThreadHelper::getThreadSearchReplace($content);
        $data['content'] = str_replace($searches, $replaces, $content);

        return $this->outPut(ResponseCode::SUCCESS, '', $data);

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
            'content' => str_replace(['<t><p>', '</p></t>'], ['', ''], $post['content']),
            'replyCount' => $post['reply_count'],
            'likeCount' => $post['like_count'],
            'createdAt' => optional($post->created_at)->format('Y-m-d H:i:s'),
            'isFirst' => $post['is_first'],
            'isComment' => $post['is_comment'],
            'isApproved' => $post['is_approved'],
            'rewards' => floatval(sprintf('%.2f', $post->getPostReward(UserWalletLog::TYPE_INCOME_THREAD_REWARD))),
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

        if (!empty($post->commentUser)) {
            $data['commentUser'] = $this->getUser($post->commentUser);

        }

        if (!empty($post->replyUser)) {
            $data['replyUser'] = $this->getUser($post->replyUser);
        }

//        if ($post->relationLoaded('commentUser')) {
//            $data['commentUser'] = $this->getUser($post->commentUser);
//        }
//
//        if ($post->relationLoaded('replyUser')) {
//            $data['replyUser'] = $this->getUser($post->replyUser);
//        }

        return $data;
    }


    private function getUser(User $user)
    {
        if (!$user) {
            return null;
        }

        $userId = $user['id'];
        $userIds = [$userId];
        $groups = GroupUser::instance()->getGroupInfo($userIds);
        $groups = array_column($groups, null, 'user_id');

        if (!empty($groups[$userId])) {
            $group = $this->getGroupInfo($groups[$userId]);
        }
        $group = [$group];

        return [
            'id' => $user['id'],
            'nickname' => $user['nickname'] ? $user['nickname'] : "",
            'groups' => $group,
            'avatar' => $user['avatar'],
            'likedCount' => $user['liked_count'],
            'isRealName' => !empty($user['realname']),
        ];
    }


    private function getGroupInfo($group)
    {
        if (!$group) {
            return null;
        }

        return [
            'groupName' => $group['groups']['name'],
            'isDisplay' => $group['groups']['is_display']
        ];
    }

}
