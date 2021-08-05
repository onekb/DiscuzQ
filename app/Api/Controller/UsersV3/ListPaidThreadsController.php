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

namespace App\Api\Controller\UsersV3;

use App\Common\ResponseCode;
use App\Models\Order;
use App\Api\Serializer\ThreadSerializer;
use App\Models\Post;
use App\Models\User;
use App\Repositories\ThreadRepository;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Discuz\Common\Utils;
use Illuminate\Contracts\Routing\UrlGenerator;

class ListPaidThreadsController extends DzqController
{
    //返回的数据一定包含的数据
    public $include = [
        'firstPost',
        'lastPostedUser',
        'category',
    ];

    public $mustInclude = [
        'user',
        'favoriteState',
        'firstPost.likeState',
    ];


    public $serializer = ThreadSerializer::class;

    protected $threads;

    protected $url;

    protected $threadCount;

    public function __construct(ThreadRepository $threads, UrlGenerator $url)
    {
        $this->threads = $threads;
        $this->url = $url;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $actor = $this->user;
        $page = $this->inPut('page') ?: 1;
        $perPage = $this->inPut('perPage') ?: 10;
        $params['isMobile'] = Utils::isMobile();
        $orderType = [Order::ORDER_TYPE_THREAD, ORDER::ORDER_TYPE_ATTACHMENT];
        $order_thread_ids = Order::query()
            ->where(['user_id' => $actor->id, 'status' => 1])
            ->whereIn('type', $orderType)
            ->pluck('thread_id');

        $thread_ids = $order_thread_ids->toArray();
       // dump($thread_ids);die;
        $query = $this->threads->query()->select('threads.*');
        $query->whereIn('id', $thread_ids);
        $query->orderBy('created_at', 'desc');
        $threads = $query->get();

        $this->threadCount = count($threads);
        $threads = $this->pagination($page, $perPage, $query);
        $threadsList = $threads['pageData'];
        $userIds = array_unique(array_column($threadsList, 'user_id'));
        $users = User::instance()->getUsers($userIds);
        $users = array_column($users, null, 'id');

        $posts = Post::query()
            ->whereIn('thread_id', $thread_ids)
            ->whereNull('deleted_at')
            ->where('is_first', Post::FIRST_YES)
            ->get()->pluck(null, 'thread_id');
       // dump($posts);exit;
        foreach ($threadsList as $thread) {
            $userId = $thread['user_id'];
            $user = [];
            $id = $thread['id'];

            if (!empty($users[$userId])) {
                $user = $this->getUserInfo($users[$userId]);
            }
            $firstPost = null;
            if (!empty($posts[$id])) {
                $firstPost = $this->getPost($posts[$id]);
            }

            $result[] = [
                'thread' =>$thread,
                'post' =>$firstPost,
                'user' => $user,
            ];
        }

        $threads['pageData'] = $result;
        $build = $this->camelData($threads);

        return $this->outPut(ResponseCode::SUCCESS,'',$build);

    }

    private function getUserInfo($user)
    {
        return [
            'pid' => $user['id'],
            'userName' => $user['username'],
            'avatar' => $user['avatar'],
            'threadCount' => $user['thread_count'],
            'followCount' => $user['follow_count'],
            'fansCount' => $user['fans_count'],
            'likedCount' => $user['liked_count'],
            'questionCount' => $user['question_count'],
            'isRealName' => !empty($user['realname']),
        ];
    }



    protected function getPost($post)
    {
        $firstPost = [
            'id' => $post['id'],
            'userId' => $post['user_id'],
            'threadId' => $post['thread_id'],
            'replyPostId' => $post['reply_post_id'],
            'replyUserId' => $post['reply_user_id'],
            'commentPostId' => $post['comment_post_id'],
            'commentUserId' => $post['comment_user_id'],
            'content' => $post['content'],
            'replyCount' => $post['reply_count'],
            'likeCount' => $post['like_count'],
            'isFirst' => $post['is_first'],
            'isComment' => $post['is_comment'],
            'isApproved' => $post['is_approved'],
            'canLike' => $this->user->can('like', $post),

        ];

        return $firstPost;
    }



}
