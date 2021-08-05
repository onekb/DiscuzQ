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
use App\Models\ThreadTopic;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Support\Arr;

class TopicListController extends DzqController
{
    use ThreadTrait;
    use ThreadListTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $filter = $this->inPut('filter');
        $currentPage = $this->inPut('page');
        $perPage = $this->inPut('perPage');

        if (Arr::has($filter, 'topicId') && Arr::get($filter, 'topicId') != 0) {
            $topicData = Topic::query()->where('id', $filter['topicId'])->first();
            if (!empty($topicData)) {
                $this->refreshTopicViewCount($topicData);
                $this->refreshTopicThreadCount($topicData);
            }
        }
        $topics = $this->filterTopics($filter, $currentPage, $perPage);
        $topicsList = $topics['pageData'];
        $topicIds = array_column($topicsList, 'id');

        $TopicThreadArray = ThreadTopic::join('threads', 'threads.id', 'thread_topic.thread_id')
            ->whereIn('thread_topic.topic_id', $topicIds)
            ->where('threads.is_approved', Thread::APPROVED)
            ->where('threads.is_draft', Thread::IS_NOT_DRAFT)
            ->whereNull('threads.deleted_at')
            ->get(['thread_id','topic_id','view_count'])
            ->toArray();
        $newTopicThreadArray = [];
        foreach ($TopicThreadArray as $key => $value) {
            $newTopicThreadArray[$value['topic_id']][] = $value;
        }
        $TopicThread = [];
        foreach ($newTopicThreadArray as $key=>$value){
            $TopicThread[$key]['thread_count'] = count($value);
            $view_count = 0;
            foreach ($value as $k=>$v){
                $view_count +=$v['view_count'];
            }
            $TopicThread[$key]['view_count'] = $view_count;
        }

        $userIds = array_column($topicsList, 'user_id');
        $userDatas = User::instance()->getUsers($userIds);
        $userDatas = array_column($userDatas, null, 'id');
        $topicThreadDatas = [];

        $threads = $this->getFilterThreads($topicIds, $filter, $currentPage, $perPage);
        if (isset($threads['pageData'])) {
            $topics['pageLength'] = $threads['pageLength'];
            $topics['totalCount'] = $threads['totalCount'];
            $topics['totalPage'] = $threads['totalPage'];
            $threads = $this->getFullThreadData($threads['pageData']);
        } else {
            $threads = $this->getFullThreadData($threads);
        }

        foreach ($threads as $key => $value) {
            $topicThreadDatas[$value['topicId']][] = $value;
        }

        $result = [];
        foreach ($topicsList as $topic) {
            $topicId = $topic['id'];
            $thread = [];
            if (isset($topicThreadDatas[$topicId])) {
                $thread = array_values($topicThreadDatas[$topicId]);
            }

            $result[] = [
                'topicId' => $topic['id'],
                'userId' => $topic['user_id'],
                'username' => $userDatas[$topic['user_id']]['username'] ?? '',
                'content' => $topic['content'],
                'viewCount' => !empty($TopicThread[$topicId]['view_count']) ? $TopicThread[$topicId]['view_count'] : 0,
                'threadCount' => !empty($TopicThread[$topicId]['thread_count']) ? $TopicThread[$topicId]['thread_count'] : 0,
                'recommended' => (bool) $topic['recommended'],
                'recommendedAt' => $topic['recommended_at'] ?? '',
                'threads' => $thread
            ];
        }

        $topics['pageData'] = $result;
        return $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($topics));
    }

    private function filterTopics($filter, $currentPage, $perPage)
    {
        $query = Topic::query();

        if ($content = trim(Arr::get($filter, 'content'))) {
            $query->where('topics.content', 'like', '%' . $content . '%');
        }

        if (Arr::has($filter, 'recommended') && Arr::get($filter, 'recommended') != '') {
            $query->where('topics.recommended', (int)Arr::get($filter, 'recommended'));
        }

        if ($topicId = trim(Arr::get($filter, 'topicId'))) {
            $query->where('topics.id', '=', $topicId);
        }

        if ((Arr::has($filter, 'hot') && Arr::get($filter, 'hot') == 1) || 
            (Arr::has($filter, 'sortBy') && Arr::get($filter, 'sortBy') == Topic::SORT_BY_VIEWCOUNT)) {
            $query->orderByDesc('topics.view_count');
        } elseif (Arr::has($filter, 'sortBy') && Arr::get($filter, 'sortBy') == Topic::SORT_BY_THREADCOUNT) {
            $query->orderByDesc('topics.thread_count');
        } elseif(Arr::has($filter, 'recommended') && Arr::get($filter, 'recommended') == Topic::TOPIC_BY_RECOMMENDED) {
            $query->orderByDesc('topics.recommended_at');
        } else{
            $query->orderByDesc('topics.created_at');
        }

        $topics = $this->pagination($currentPage, $perPage, $query);
        return $topics;
    }

    function getFilterThreads($topicIds, $filter, $currentPage, $perPage)
    {
        $categoryids = [];
        $categoryids = Category::instance()->getValidCategoryIds($this->user, $categoryids);
        if (!$categoryids) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '没有内容浏览权限');
        }

        if (!Arr::has($filter, 'topicId') || Arr::get($filter, 'topicId') == 0) {
            $threadTopics = ThreadTopic::query()
                ->selectRaw(' `topic_id`, MAX(`thread_id`) as thread_id')
                ->join('threads', 'id', '=', 'thread_id')
                ->where('threads.is_sticky', Thread::BOOL_NO)
                ->where('threads.is_draft', Thread::IS_NOT_DRAFT)
                ->where('threads.is_approved', Thread::APPROVED)
                ->whereNull('threads.deleted_at')
                ->whereNotNull('threads.user_id')
                ->whereIn('threads.category_id', $categoryids)
                ->whereIn('thread_topic.topic_id', $topicIds)
                ->groupBy('thread_topic.topic_id')
                ->get();
            $threadIds = $threadTopics->pluck('thread_id')->toArray();
            $threads = Thread::query()
                ->select('threads.*', 'thread_topic.topic_id')
                ->leftJoin('thread_topic', 'thread_topic.thread_id', '=', 'threads.id')
                ->whereIn('threads.id', $threadIds)->get()->toArray();
            return  $threads;
        }

        $query = Thread::query();
        $query->join('thread_topic', 'thread_topic.thread_id', '=', 'threads.id');
        $query->where('threads.is_sticky', Thread::BOOL_NO);
        $query->where('threads.is_draft', Thread::IS_NOT_DRAFT);
        $query->where('threads.is_approved', Thread::APPROVED);
        $query->whereNull('threads.deleted_at');
        $query->whereNotNull('threads.user_id');
        $query->whereIn('threads.category_id', $categoryids);
        $query->whereIn('thread_topic.topic_id', $topicIds);
        $query->orderByDesc('threads.created_at');
        $threads = $this->pagination($currentPage, $perPage, $query);
        return $threads;
    }

    /**
     * refresh thread count
     * 用户删除、帖子审核、帖子逻辑删除、帖子草稿不计算
     */
    private function refreshTopicThreadCount($topicData)
    {
        $threadCount = ThreadTopic::join('threads', 'threads.id', 'thread_topic.thread_id')
            ->where('thread_topic.topic_id', $topicData->id)
            ->where('threads.is_approved', Thread::APPROVED)
            ->where('threads.is_draft', Thread::IS_NOT_DRAFT)
            ->whereNull('threads.deleted_at')
            ->whereNotNull('user_id')
            ->count();
        $topicData->thread_count = $threadCount;
        $topicData->save();
    }

    /**
     * refresh view count
     * 帖子审核、帖子逻辑删除、帖子草稿不计算
     */
    private function refreshTopicViewCount($topicData)
    {
        $viewCount = ThreadTopic::join('threads', 'threads.id', 'thread_topic.thread_id')
            ->where('thread_topic.topic_id', $topicData->id)
            ->where('threads.is_approved', Thread::APPROVED)
            ->where('threads.is_draft', Thread::IS_NOT_DRAFT)
            ->whereNull('threads.deleted_at')
            ->sum('view_count');
        $topicData->view_count = $viewCount;
        $topicData->save();
    }
}
