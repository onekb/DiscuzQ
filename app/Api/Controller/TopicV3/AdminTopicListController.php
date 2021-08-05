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

namespace App\Api\Controller\TopicV3;

use App\Api\Controller\ThreadsV3\ThreadTrait;
use App\Api\Controller\ThreadsV3\ThreadListTrait;
use App\Common\ResponseCode;
use App\Models\Category;
use App\Models\Topic;
use App\Models\Thread;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Support\Arr;

class AdminTopicListController extends DzqController
{
    use ThreadTrait;
    use ThreadListTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有访问话题列表的权限');
        }
        return true;
    }

    public function main()
    {
        $filter = $this->inPut('filter');
        $currentPage = $this->inPut('page');
        $perPage = $this->inPut('perPage');
        $topics = $this->filterTopics($filter, $currentPage, $perPage);
        $topicsList = $topics['pageData'];
        $topicIds = array_column($topicsList, 'id');
        $userIds = array_column($topicsList, 'user_id');
        $userDatas = User::instance()->getUsers($userIds);
        $userDatas = array_column($userDatas, null, 'id');
        $topicThreadDatas = [];

        $threads = $this->getFilterThreads($topicIds);
        $threads = $this->getFullThreadData($threads);

        foreach ($threads as $key => $value) {
            $topicThreadDatas[$value['topicId']][$value['threadId']] = $value;
        }

        $result = [];
        foreach ($topicsList as $topic) {
            $topicId = $topic['id'];
            $thread = [];

            if (isset($topicThreadDatas[$topicId]) && $topicId == Arr::has($filter, 'topicId')) {
                $thread = array_values($topicThreadDatas[$topicId]);
            }

            $result[] = [
                'topicId' => $topic['id'],
                'userId' => $topic['user_id'],
                'username' => $userDatas[$topic['user_id']]['username'] ?? '',
                'content' => $topic['content'],
                'viewCount' => $topic['view_count'],
                'threadCount' => $topic['thread_count'],
                'createdAt' => date("Y-m-d H:i:s", strtotime($topic['created_at'])),
                'recommended' => (bool) $topic['recommended'],
                'recommendedAt' => $topic['recommended_at'] ? date("Y-m-d H:i:s", strtotime($topic['recommended_at'])): '',
                'threads' => $thread
            ];
        }

        $topics['pageData'] = $result;
        return $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($topics));
    }

    private function filterTopics($filter, $currentPage, $perPage)
    {
        $query = Topic::query();

        if ($username = trim(Arr::get($filter, 'username'))) {
            $query->join('users', 'users.id', '=', 'topics.user_id')
                ->where('users.username', 'like', '%' . $username . '%')
                ->selectRaw("topics.*,users.id as uid");
        }

        if ($content = trim(Arr::get($filter, 'content'))) {
            $query->where('topics.content', 'like', '%' . $content . '%');
        }

        if ($createdAtBegin = Arr::get($filter, 'createdAtBegin')) {
            $query->where('topics.created_at', '>=', $createdAtBegin);
        }

        if ($createdAtEnd = Arr::get($filter, 'createdAtEnd')) {
            $createdAtEnd =  date("Y-m-d",strtotime("+1 day",strtotime($createdAtEnd)));
            $query->where('topics.created_at', '<=', $createdAtEnd);
        }

        if ($threadCountBegin = Arr::get($filter, 'threadCountBegin')) {
            $query->where('topics.thread_count', '>=', $threadCountBegin);
        }

        if ($threadCountEnd = Arr::get($filter, 'threadCountEnd')) {
            $query->where('topics.thread_count', '<=', $threadCountEnd);
        }

        if ($viewCountBegin = Arr::get($filter, 'viewCountBegin')) {
            $query->where('topics.view_count', '>=', $viewCountBegin);
        }

        if ($viewCountEnd = Arr::get($filter, 'viewCountEnd')) {
            $query->where('topics.view_count', '<=', $viewCountEnd);
        }

        if (Arr::has($filter, 'recommended') && Arr::get($filter, 'recommended') != '') {
            $query->where('topics.recommended', (int)Arr::get($filter, 'recommended'));
        }

        if ($topicId = trim(Arr::get($filter, 'topicId'))) {
            $query->where('topics.id', '=', $topicId);
        }

        if ((Arr::has($filter, 'sortBy') && Arr::get($filter, 'sortBy') == Topic::SORT_BY_VIEWCOUNT)) {
            $query->orderByDesc('topics.view_count');
        } elseif (Arr::has($filter, 'sortBy') && Arr::get($filter, 'sortBy') == Topic::SORT_BY_THREADCOUNT) {
            $query->orderByDesc('topics.thread_count');
        } else{
            $query->orderByDesc('topics.created_at');
        }

        $topics = $this->pagination($currentPage, $perPage, $query);
        return $topics;
    }

    function getFilterThreads($topicIds)
    {
        $categoryids = [];
        $categoryids = Category::instance()->getValidCategoryIds($this->user, $categoryids);
        if (!$categoryids) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '没有内容浏览权限');
        }
        $threads = $this->getThreadsBuilder($topicIds);
        !empty($categoryids) && $threads->whereIn('category_id', $categoryids);
        return $threads->get()->toArray();
    }

    private function getThreadsBuilder($topicIds)
    {
        return Thread::query()
            ->from('threads as th')
            ->join('thread_topic as tt', 'tt.thread_id', '=', 'th.id')
            ->whereNull('th.deleted_at')
            ->where('th.is_sticky', Thread::BOOL_NO)
            ->where('th.is_draft', Thread::IS_NOT_DRAFT)
            ->where('th.is_approved', Thread::APPROVED)
            ->whereIn('tt.topic_id', $topicIds)
            ->orderByDesc('th.created_at');
    }
}
