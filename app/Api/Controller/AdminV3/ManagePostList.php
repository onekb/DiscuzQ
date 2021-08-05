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

use App\Api\Serializer\AttachmentSerializer;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use App\Models\Attachment;
use App\Models\UserActionLogs;
use App\Models\Post;
use App\Models\StopWord;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Support\Str;

class ManagePostList extends DzqController
{

    protected $attachmentSerializer;

    private $sortFields = [
        'id',
        'reply_count',
        'like_count',
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

    public function __construct( AttachmentSerializer $attachmentSerializer) {
        $this->attachmentSerializer = $attachmentSerializer;
    }

    public function main()
    {
        $this->attachmentSerializer->setRequest($this->request);

        $isDeleted = $this->inPut('isDeleted'); //是否删除
        $nickname = $this->inPut('nickname'); //用户名
        $page = intval($this->inPut('page')); //分页
        $perPage = intval($this->inPut('perPage')); //分页
        $q = $this->inPut('q'); //内容
        $isApproved = $this->inPut('isApproved') ? intval($this->inPut('isApproved')) : 0; //0未审核 1已忽略
        $createdAtBegin = $this->inPut('createdAtBegin'); //开始时间
        $createdAtEnd = $this->inPut('createdAtEnd'); //结束时间
        $deletedAtBegin = $this->inPut('deletedAtBegin'); //删除开始时间
        $deletedAtEnd = $this->inPut('deletedAtEnd'); //删除结束时间
        $deletedNickname = $this->inPut('deletedNickname'); //删除帖子用户
        $categoryId = intval($this->inPut('categoryId')); //分类id
        $highlight = $this->inPut('highlight');  //是否显示敏感词
        $sort = $this->inPut('sort') ? $this->inPut('sort') : '-updated_at';//排序

        $query = Post::query()
            ->select(
                'posts.id as post_id', 'posts.thread_id', 'posts.user_id','posts.content', 'posts.ip',
                'posts.updated_at', 'posts.deleted_user_id' ,'posts.deleted_at',
                'threads.title',
                'users.nickname'
            )
            ->where('posts.is_first',false);

        $query->leftJoin('threads', 'posts.thread_id', '=', 'threads.id');

        // 回收站
        if ($isDeleted == 'yes') {
            // 只看回收站帖子
            $query->whereNotNull('posts.deleted_at')
                ->addSelect('users1.nickname as deleted_nickname')
                ->leftJoin('users as users1', 'users1.id','=','posts.deleted_user_id');
        } elseif ($isDeleted == 'no') {
            // 不看回收站帖子
            $query->where('posts.is_approved', $isApproved)
                ->whereNull('posts.deleted_at');
        }

        //用户昵称筛选
        $query->leftJoin('users', 'users.id', '=', 'posts.user_id');
        if (!empty($nickname)) {
            $query->where('users.nickname', 'like','%'.$nickname.'%');
        }

        //内容筛选
        if (!empty($q)) {
            $query->where('posts.content','like','%'.$q.'%');
        }

        //发帖删除时间筛选
        if (!empty($deletedAtBegin) && !empty($deletedAtEnd)) {
            $query->whereBetween('posts.deleted_at', [$deletedAtBegin, $deletedAtEnd]);
        }

        //用户删除用户昵称
        if (!empty($deletedNickname)) {
            $query->where('users1.nickname','like','%'.$deletedNickname.'%');
        }

        //时间筛选
        if (!empty($createdAtBegin) && !empty($createdAtEnd)) {
            $query->whereBetween('posts.updated_at', [$createdAtBegin, $createdAtEnd]);
        }

        //分类筛选
        if (!empty($categoryId)) {
            $query->where('threads.category_id', $categoryId);
        }

        //排序
        $sortDetect = ltrim(Str::snake($sort), '-');
        if (!in_array($sortDetect, $this->sortFields)) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '不合法的排序字段:'.$sortDetect);
        }

        $query = $query->orderBy('posts.'.$sortDetect,
        Str::startsWith($sort, '-') ? 'desc' : 'asc');

        $pagination = $this->pagination($page, $perPage, $query, false);

        // 高亮敏感词
        if ($highlight == 'yes') {
            $stopWord = StopWord::query()->where('ugc',StopWord::MOD)->get(['find'])->toArray();
            $replace = array_column($stopWord, 'find');

            foreach ($pagination['pageData'] as $key=>$val){
                foreach ($replace as $v){
                    $val['content']  = str_replace($v,'<span class="highlight">' . $v . '</span>',$val['content']);
                }
                $pagination['pageData'][$key]['content'] = $val['content'];
            }
        }

        $this->outPut(ResponseCode::SUCCESS,'', $this->camelData($this->paramHandle($pagination)));
    }


    public function paramHandle($pagination)
    {
        $pageData = $pagination['pageData'];

        $isDeleted = $this->inPut('isDeleted');

        $userActionLogs = [];

        if ($isDeleted == 'yes') {
            $userIds = array_column($pageData->toArray(),'post_id');

            $userActionLogs = UserActionLogs::query()
                ->whereIn('log_able_id',$userIds)
                ->where(['action' => 'hide', 'log_able_type' => 'App\Models\Post'])
                ->orderBy('id','desc')
                ->get(['log_able_id as post_id','message'])
                ->pluck('message','post_id')
                ->toArray();
        }

        foreach ($pageData as $k=>$v) {
            if (!isset($userActionLogs[$v['post_id']])) {
                $userActionLogs[$v['post_id']] = '';
            }

            if (!isset($v['deleted_nickname'])) {
                $pageData[$k]['deleted_nickname'] = '';
            }

            $pageData[$k]['updatedAt'] = date('Y-m-d H:i:s',strtotime($pageData[$k]['updated_at']));

            $pageData[$k]['lastDeletedLog'] = [
                'message' => $userActionLogs[$v['post_id']]
            ];

            $pageData[$k]['deletedUserArr'] = [
              'deletedNickname' => $pageData[$k]['deleted_nickname'],
              'deletedAt' => date('Y-m-d H:i:s',strtotime($pageData[$k]['deleted_at'])),
              'deletedUserId' => $pageData[$k]['deleted_user_id'],
            ];

            $v['id'] = $v['post_id'];
            $pageData[$k]['content'] = [
                'text' => $v['content'],
                'indexes' => $v->images->map(function (Attachment $image) {
                    return $this->attachmentSerializer->getDefaultAttributes($image);
                }),
            ];

            unset(
                $pageData[$k]['id'],
                $pageData[$k]['images'],
                $pageData[$k]['deleted_nickname'],
                $pageData[$k]['deleted_at'],
                $pageData[$k]['deleted_user_id']
            );
        }

        $pagination['pageData'] = $pageData;

        return $pagination;
    }

}
