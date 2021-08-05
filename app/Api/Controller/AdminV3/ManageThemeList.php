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

namespace App\Api\Controller\AdminV3;

use App\Api\Controller\ThreadsV3\ThreadTrait;
use App\Api\Controller\ThreadsV3\ThreadListTrait;
use App\Common\ResponseCode;
use App\Models\Post;
use App\Models\UserActionLogs;
use App\Repositories\UserRepository;
use App\Models\Thread;
use App\Models\StopWord;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Support\Str;

class ManageThemeList extends DzqController
{
    use ThreadTrait;
    use ThreadListTrait;

    private $sortFields = [
        'id',
        'is_sticky',
        'post_count',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $threadType = $this->inPut('threadType'); //置顶加精类型
        $viewCountGt = $this->inPut('viewCountGt'); //浏览次数始
        $viewCountLt = $this->inPut('viewCountLt'); //浏览次数结
        $postCountGt = $this->inPut('postCountGt'); //回复次数始
        $postCountLt = $this->inPut('postCountLt'); //回复次数结
        $highlight = $this->inPut('highlight');  //是否显示敏感词
        $isApproved = $this->inPut('isApproved') ? intval($this->inPut('isApproved')) : 0; //0未审核 1已忽略
        $threadId = intval($this->inPut('threadId')); // 帖子id
        $q = $this->inPut('q'); //内容
        $isDeleted = $this->inPut('isDeleted'); //帖子是否删除
        $nickname = $this->inPut('nickname'); //用户名
        $page = intval($this->inPut('page')); //分页
        $perPage = intval($this->inPut('perPage')); //分页
        $createdAtBegin = $this->inPut('createdAtBegin'); //开始时间
        $createdAtEnd = $this->inPut('createdAtEnd'); //结束时间
        $deletedAtBegin = $this->inPut('deletedAtBegin'); //删除开始时间
        $deletedAtEnd = $this->inPut('deletedAtEnd'); //删除结束时间
        $deletedNickname = $this->inPut('deletedNickname'); //删除帖子用户
        $categoryId = intval($this->inPut('categoryId')); //分类id
        $sort = $this->inPut('sort') ? $this->inPut('sort') : '-updated_at';     //排序

        $query = Thread::query()
            ->select(
                'threads.*'
            );

        //是否审核and是否草稿
        $query->where('threads.is_draft', Thread::IS_NOT_DRAFT);

        //浏览次数
        if ($viewCountGt !== '') {
            $query->where('threads.view_count', '>=', intval($viewCountGt));
        }

        //浏览次数
        if ($viewCountLt !== '') {
            $query->where('threads.view_count', '<=', intval($viewCountLt));
        }

        //回复次数
        if ($postCountGt !== '') {
            $query->where('threads.post_count', '>=', intval($postCountGt));
        }

        //回复次数
        if ($postCountLt !== '') {
            $query->where('threads.post_count', '<=', intval($postCountLt));
        }

        /*
         * 置顶 1
         * 加精 2
         * 置顶并精华主题 3
         * 付费首页主题 4
         */
        if ($threadType == 1) {
            $query->where('threads.is_sticky', 1);
        } else if ($threadType == 2) {
            $query->where('threads.is_essence', 1);
        } else if ($threadType == 3) {
            $query->where('threads.is_sticky', 1)
                ->where('threads.is_essence', 1);
        } else if ($threadType == 4){
            $query->where('threads.is_site', 1)
                ->where(function ($query) {
                    $query->orWhere('threads.price', '>', 0)
                        ->orWhere('threads.attachment_price', '>' ,0);
                });
        }

        //帖子id筛选
        if (!empty($threadId)) {
            $query->where('threads.id', $threadId);
        }

        //内容筛选
        if (!empty($q)) {
            $query->where('threads.title','like','%'.$q.'%');
        }

        // 回收站
        if ($isDeleted == 'yes') {
            // 只看回收站帖子
            $query->whereNotNull('threads.deleted_at')
                ->addSelect('users1.nickname as deleted_user')
                ->leftJoin('users as users1', 'users1.id','=','threads.deleted_user_id');
        } elseif ($isDeleted == 'no') {
            // 不看回收站帖子
            $query->where('threads.is_approved', $isApproved)
                ->whereNull('threads.deleted_at');
        }

        //类型筛选
        $query->leftJoin('categories', 'categories.id', '=', 'threads.category_id');
        if (!empty($categoryId)) {
            $query->where('threads.category_id', $categoryId);
        }

        //发帖时间筛选
        if (!empty($createdAtBegin) && !empty($createdAtEnd)) {
            $query->whereBetween('threads.updated_at', [$createdAtBegin, $createdAtEnd]);
        }

        //发帖删除时间筛选
        if (!empty($deletedAtBegin) && !empty($deletedAtEnd)) {
            $query->whereBetween('threads.deleted_at', [$deletedAtBegin, $deletedAtEnd]);
        }

        //用户删除用户昵称
        if (!empty($deletedNickname)) {
            $query->where('users1.nickname','like','%'.$deletedNickname.'%');
        }

        //用户昵称筛选
        $query->leftJoin('users', 'users.id', '=', 'threads.user_id');
        if (!empty($nickname)) {
            $query->where('users.nickname', 'like','%'.$nickname.'%');
        }

        //排序
        $sortDetect = ltrim(Str::snake($sort), '-');
        if (!in_array($sortDetect, $this->sortFields)) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '不合法的排序字段:'.$sortDetect);
        }

        //排序
        $query = $query->orderBy('threads.'.$sortDetect,
            Str::startsWith($sort, '-') ? 'desc' : 'asc');

        //分页
        $pagination = $this->pagination($page, $perPage, $query);

        $snapArr = array_column($pagination['pageData'],null,'id');

        $pagination['pageData'] = $this->getFullThreadData($pagination['pageData']);

        $this->outPut(ResponseCode::SUCCESS,'', $this->paramHandle($snapArr, $pagination));
    }

    public function paramHandle($snapArr, $pagination)
    {
        $isDeleted = $this->inPut('isDeleted');
        $highlight = $this->inPut('highlight');  //是否显示敏感词

        $replace = [];
        if ($highlight == 'yes') {
            $stopWord = StopWord::query()->where('ugc',StopWord::MOD)->get(['find'])->toArray();
            $replace = array_column($stopWord, 'find');
        }

        $pageData = $pagination['pageData'];

        $userActionLogs = [];

        $threadsIds = array_column($snapArr,'id');

        if ($isDeleted == 'yes') {

            $userActionLogs = UserActionLogs::query()
                ->whereIn('log_able_id',$threadsIds)
                ->where(['action' => 'hide', 'log_able_type' => 'App\Models\Thread'])
                ->orderBy('id','desc')
                ->get(['log_able_id as thread_id','message'])
                ->pluck('message','thread_id')
                ->toArray();
        }

        $post = Post::query()
            ->whereIn('thread_id',$threadsIds)
            ->leftJoin('users','posts.user_id','=','users.id')
            ->orderBy('posts.created_at','asc')
            ->get(['users.nickname','posts.user_id','posts.thread_id','posts.created_at','posts.content','posts.is_first'])
            ->toArray();

        /*
         * $content 主题内容
         * $lastPostedUser 最后回复用户
         */
        $content = [];
        $lastPostedUser = [];
        foreach ($post as $k=>$v) {
            if ( $v['is_first'] == 1 ) {
                $content[$v['thread_id']] = $post[$k];
            }
            if ( $v['is_first'] == 0 ) {
                $lastPostedUser[$v['thread_id']] = $post[$k];
            }
        }

        //参数归类
        foreach ($pageData as $k => $v) {
            if (!isset($userActionLogs[$v['threadId']])) {
                $userActionLogs[$v['threadId']] = '';
            }

            if (!isset($snapArr[$v['threadId']]['deleted_user'])) {
                $snapArr[$v['threadId']]['deleted_user'] = '';
            }

            $pageData[$k]['lastDeletedLog'] = [
                'message' => isset($userActionLogs[$v['threadId']]) ? $userActionLogs[$v['threadId']] : null
            ];

            $pageData[$k]['lastPostedUser'] = [
                'lastNickname' => isset($lastPostedUser[$v['threadId']]['nickname']) ? $lastPostedUser[$v['threadId']]['nickname'] : null,
                'userId' => isset($lastPostedUser[$v['threadId']]['user_id']) ? $lastPostedUser[$v['threadId']]['user_id'] : null,
                'createdAt' => isset($lastPostedUser[$v['threadId']]['created_at']) ? date('Y-m-d H:i:s',strtotime($lastPostedUser[$v['threadId']]['created_at'])) : null,
            ];

            $pageData[$k]['deletedUserArr'] = [
                'deletedNickname' => isset($snapArr[$v['threadId']]['deleted_user']) ? $snapArr[$v['threadId']]['deleted_user'] : null,
                'deletedAt' => isset($snapArr[$v['threadId']]['deleted_at']) ? date('Y-m-d H:i:s',strtotime($snapArr[$v['threadId']]['deleted_at'])) : null,
                'deletedUserId' => isset($snapArr[$v['threadId']]['deleted_user_id']) ? $snapArr[$v['threadId']]['deleted_user_id'] : null
            ];

            if (!empty($replace)){
                foreach ($replace as $val){
                    $content[$v['threadId']]['content'] = str_replace($val,'<span class="highlight">' . $val . '</span>',$content[$v['threadId']]['content']);
                }
                $pageData[$k]['content']['text'] = isset($content[$v['threadId']]['content']) ? $content[$v['threadId']]['content'] : null;
            }
            $pageData[$k]['content']['text'] = isset($content[$v['threadId']]['content']) ? $content[$v['threadId']]['content'] : null;
        }

        $pagination['pageData'] = $pageData;

        return $pagination;
    }

}
