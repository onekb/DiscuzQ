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

namespace App\Api\Controller\ThreadsV3;


use App\Models\Category;
use App\Models\DenyUser;
use App\Models\Order;
use App\Models\Post;
use App\Models\Sequence;
use App\Models\Thread;
use App\Models\ThreadTopic;
use Carbon\Carbon;

trait ThreadQueryTrait
{
    /**
     * @desc 普通筛选SQL
     * @param $filter
     * @param bool $withLoginUser
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildFilterThreads($filter, &$withLoginUser = false)
    {
        list($essence, $types, $sort, $attention, $search, $complex, $categoryids) = $this->initFilter($filter);
        $loginUserId = $this->user->id;
        $administrator = $this->user->isAdmin();
        $threads = $this->getBaseThreadsBuilder();
        if (!empty($complex)) {
            switch ($complex) {
                case Thread::MY_DRAFT_THREAD:
                    $threads = $this->getBaseThreadsBuilder(Thread::IS_DRAFT, false)
                        ->where('th.user_id', $loginUserId)
                        ->orderByDesc('th.id');
                    $threads = $threads->join('posts as post', 'post.thread_id', '=', 'th.id');
                    break;
                case Thread::MY_LIKE_THREAD:
                    empty($filter['toUserId']) ? $userId = $loginUserId : $userId = intval($filter['toUserId']);
                    $threads = $threads->leftJoin('posts as post', 'post.thread_id', '=', 'th.id')
                        ->where(['post.is_first' => Post::FIRST_YES, 'post.is_approved' => Post::APPROVED_YES])
                        ->leftJoin('post_user as postu', 'postu.post_id', '=', 'post.id')
                        ->where(['postu.user_id' => $userId])
                        ->orderByDesc('postu.created_at');
                    break;
                case Thread::MY_COLLECT_THREAD:
                    $threads = $threads->leftJoin('thread_user as thu', 'thu.thread_id', '=', 'th.id')
                        ->where(['thu.user_id' => $loginUserId])
                        ->orderByDesc('thu.created_at');
                    break;
                case Thread::MY_BUY_THREAD:
                    $threads = $threads->leftJoin('orders as order', 'order.thread_id', '=', 'th.id')
                        ->whereIn('order.type', [Order::ORDER_TYPE_THREAD, Order::ORDER_TYPE_ATTACHMENT])
                        ->where(['order.user_id' => $loginUserId, 'order.status' => Order::ORDER_STATUS_PAID])
                        ->orderByDesc('order.updated_at');
                    break;
                case Thread::MY_OR_HIS_THREAD:
                    if (empty($filter['toUserId']) || $filter['toUserId'] == $loginUserId || $administrator) {
                        $threads = $this->getBaseThreadsBuilder(Thread::BOOL_NO, false);
                    } else {
                        $threads = $threads->where('th.is_anonymous', Thread::IS_NOT_ANONYMOUS);
                    }
                    empty($filter['toUserId']) ? $userId = $loginUserId : $userId = intval($filter['toUserId']);
                    $threads = $threads->where('user_id', $userId)
                        ->orderByDesc('th.id');
                    break;
            }
            $withLoginUser = true;
        }
        !empty($essence) && $threads = $threads->where('is_essence', $essence);
        if (!empty($types)) {
            $threads = $threads->leftJoin('thread_tag as tag', 'tag.thread_id', '=', 'th.id')
                ->whereIn('tag', $types);
        }
        if (!empty($search)) {
            $threads = $threads->leftJoin('posts as post', 'th.id', '=', 'post.thread_id')
                ->addSelect('post.content')
                ->where(['post.is_first' => Post::FIRST_YES, 'post.is_approved' => Post::APPROVED_YES])
                ->whereNull('post.deleted_at')
                ->where(function ($threads) use ($search) {
                    $threads->where('th.title', 'like', '%' . $search . '%');
                    $threads->orWhere('post.content', 'like', '%' . $search . '%');
                });
        }
        if (!empty($sort)) {
            switch ($sort) {
                case Thread::SORT_BY_THREAD://按照发帖时间排序
                    $threads->orderByDesc('th.created_at');
                    break;
                case Thread::SORT_BY_POST://按照评论时间排序
                    $threads->orderByDesc('th.posted_at');
                    break;
                case Thread::SORT_BY_HOT://按照热度排序
                    $threads->whereBetween('th.created_at', [Carbon::parse('-7 days'), Carbon::now()]);
                    $threads->orderByDesc('th.view_count');
                    break;
                case Thread::SORT_BY_RENEW://按照更新时间排序
                    $threads->orderByDesc('th.updated_at');
                    break;
                default:
                    $threads->orderByDesc('th.id');
                    break;
            }
        }
        //关注
        if ($attention == 1 && !empty($this->user)) {
            $threads->leftJoin('user_follow as follow', 'follow.to_user_id', '=', 'th.user_id')
                ->where('th.is_anonymous', Thread::BOOL_NO)
                ->where('follow.from_user_id', $this->user->id);
            $withLoginUser = true;
        }
        //deny用户
        if (!empty($loginUserId)) {
            $denyUserIds = DenyUser::query()->where('user_id', $loginUserId)->get()->pluck('deny_user_id')->toArray();
            if (!empty($denyUserIds)) {
                $threads = $threads->whereNotIn('th.user_id', $denyUserIds);
                $withLoginUser = true;
            }
        }
        if (!empty($exclusiveIds)) {
            $threads = $threads->whereNotIn('th.id', $exclusiveIds);
        }
        !empty($categoryids) && $threads->whereIn('th.category_id', $categoryids);
        return $threads;
    }

    /**
     * @desc 智能排序SQL
     * @param $filter
     * @return bool|\Illuminate\Database\Eloquent\Builder
     */
    private function buildSequenceThreads($filter)
    {
        $sequence = Sequence::getSequence();
        if (empty($sequence)) {
            return $this->buildFilterThreads($filter);
        }
        $categoryIds = [];
        !empty($sequence['category_ids']) && $categoryIds = explode(',', $sequence['category_ids']);
        $categoryIds = array_map('intval', $categoryIds);
        $allowCategoryIds = Category::instance()->getValidCategoryIds($this->user);
        if (empty($filter)) $filter = [];
        isset($filter['types']) && $types = $filter['types'];
        $groupIds = [];
        $topicIds = [];
        $userIds = [];
        $threadIds = [];
        $blockUserIds = [];
        $blockThreadIds = [];
        $blockTopicIds = [];
        !empty($sequence['group_ids']) && $groupIds = explode(',', $sequence['group_ids']);
        !empty($sequence['user_ids']) && $userIds = explode(',', $sequence['user_ids']);
        !empty($sequence['topic_ids']) && $topicIds = explode(',', $sequence['topic_ids']);
        !empty($sequence['thread_ids']) && $threadIds = explode(',', $sequence['thread_ids']);
        !empty($sequence['block_user_ids']) && $blockUserIds = explode(',', $sequence['block_user_ids']);
        !empty($sequence['block_topic_ids']) && $blockTopicIds = explode(',', $sequence['block_topic_ids']);
        !empty($sequence['block_thread_ids']) && $blockThreadIds = explode(',', $sequence['block_thread_ids']);

        $query = $this->getBaseThreadsBuilder();
        $query->leftJoin('group_user as g1', 'g1.user_id', '=', 'th.user_id');
        $query->leftJoin('thread_topic as topic', 'topic.thread_id', '=', 'th.id');
        if (!empty($types)) {
            $query->leftJoin('thread_tag as tag', 'tag.thread_id', '=', 'th.id')
                ->whereIn('tag.tag', $types);
        }
        if (!empty($allowCategoryIds)) {
            $query->whereIn('th.category_id', $allowCategoryIds);
        }
        //并集threadId
        if(!empty($categoryIds)|| !empty($groupIds) || !empty($userIds) || !empty($threadIds) || !empty($topicIds)){
            $queryMerge = Thread::query()->select('th.id')->from('threads as th')
                ->leftJoin('group_user as g1', 'g1.user_id', '=', 'th.user_id')
                ->leftJoin('thread_topic as topic', 'topic.thread_id', '=', 'th.id');
            if (!empty($categoryIds)) {
                $queryMerge->orWhereIn('th.category_id', $categoryIds);
            }
            if (!empty($groupIds)) {
                $queryMerge->orWhereIn('g1.group_id', $groupIds);
            }
            if(!empty($userIds)){
                $queryMerge->orWhereIn('th.user_id', $userIds);
            }
            if(!empty($threadIds)){
                $queryMerge->orWhereIn('th.id', $threadIds);
            }
            if (!empty($topicIds)) {
                $queryMerge->orWhereIn('topic.topic_id', $topicIds);
            }
            $mergeIds = array_column($queryMerge->get()->toArray(),'id');
            $query->whereIn('th.id',$mergeIds);
        }
        //需剔除的threadId
        if(!empty($blockUserIds) || !empty($blockThreadIds) || !empty($blockTopicIds)){
            $queryRemove = Thread::query()->select('threads.id');
            if (!empty($blockUserIds)) {
                $queryRemove->orWhereIn('threads.user_id', $blockUserIds);
            }
            if (!empty($blockThreadIds)) {
                $queryRemove->orWhereIn('threads.id', $blockThreadIds);
            }
            if (!empty($blockTopicIds)) {
                $thIds = ThreadTopic::query()->distinct(true)->whereIn('topic_id',$blockTopicIds)->get("thread_id")->toArray();
                $thIds = array_column($thIds,'thread_id');
                $queryRemove->orWhereIn('threads.id', $thIds);
            }
            $removeIds = array_column($queryRemove->get()->toArray(),'id');
            $query->whereNotIn('th.id',$removeIds);
        }
        if(!empty($filter['categoryids'])){
            $query->whereIn('th.category_id', $filter['categoryids']);
        }
        $query->orderBy('th.created_at', 'desc');
        $query->distinct(true);
        return $query;
    }


    /**
     * @desc 发现页搜索结果数据
     * @param $filter
     * @param bool $withLoginUser
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildSearchThreads($filter, &$withLoginUser = false)
    {
        list($essence, $types, $sort, $attention, $search, $complex, $categoryids) = $this->initFilter($filter);
        $loginUserId = $this->user->id;
        $threadsByHot = $this->getBaseThreadsBuilder();
        if (!empty($search)) {
            $threadsByHot->leftJoin('posts as post', 'th.id', '=', 'post.thread_id')
                ->addSelect('post.content')
                ->where(['post.is_first' => Post::FIRST_YES, 'post.is_approved' => Post::APPROVED_YES])
                ->whereNull('post.deleted_at')
                ->where(function ($threads) use ($search) {
                    $threads->where('th.title', 'like', '%' . $search . '%');
                    $threads->orWhere('post.content', 'like', '%' . $search . '%');
                });
        }
        if (!empty($loginUserId)) {
            $denyUserIds = DenyUser::query()->where('user_id', $loginUserId)->get()->pluck('deny_user_id')->toArray();
            if (!empty($denyUserIds)) {
                $threadsByHot->whereNotIn('th.user_id', $denyUserIds);
                $withLoginUser = true;
            }
        }
        !empty($categoryids) && $threadsByHot->whereIn('th.category_id', $categoryids);

        $threadsByHot->whereBetween('th.created_at', [Carbon::parse('-7 days'), Carbon::now()])
            ->orderByDesc('th.view_count')->limit(10)->offset(0);
        $threadsByHotIds = $threadsByHot->get()->pluck('id');
        $threadsByUpdate = $this->getBaseThreadsBuilder();
        if (!empty($search)) {
            $threadsByUpdate->leftJoin('posts as post', 'th.id', '=', 'post.thread_id')
                ->addSelect('post.content')
                ->where(['post.is_first' => Post::FIRST_YES, 'post.is_approved' => Post::APPROVED_YES])
                ->whereNull('post.deleted_at')
                ->where(function ($threads) use ($search) {
                    $threads->where('th.title', 'like', '%' . $search . '%');
                    $threads->orWhere('post.content', 'like', '%' . $search . '%');
                });
        }
        if (!empty($loginUserId)) {
            $denyUserIds = DenyUser::query()->where('user_id', $loginUserId)->get()->pluck('deny_user_id')->toArray();
            if (!empty($denyUserIds)) {
                $threadsByUpdate->whereNotIn('th.user_id', $denyUserIds);
                $withLoginUser = true;
            }
        }
        $threadsByUpdate->whereNotIn('th.id', $threadsByHotIds);
        !empty($categoryids) && $threadsByUpdate->whereIn('th.category_id', $categoryids);
        $threadsByUpdate->orderByDesc('th.updated_at')->limit(9999999999);
        return $threadsByHot->unionAll($threadsByUpdate->getQuery());
    }


    /**
     * @desc 付费站首页帖子数据,最多显示10条
     */
    private function buildPaidHomePageThreads()
    {
        $maxCount = 10;
        $threadsBySite = $this->getBaseThreadsBuilder();
        $threadsBySite->where('th.is_site', Thread::IS_SITE);
        $threadsBySite->orderByDesc('th.view_count');
        if ($threadsBySite->count() >= $maxCount) {
            return $threadsBySite;
        }
        $threadsBySiteIds = $threadsBySite->get()->pluck('id');
        $threadsByHot = $this->getBaseThreadsBuilder();
        $threadsByHot->whereBetween('th.created_at', [Carbon::parse('-7 days'), Carbon::now()])
            ->whereNotIn('id', $threadsBySiteIds)
            ->orderByDesc('th.view_count')
            ->limit($maxCount)->offset(0);
        $threadsBySite->unionAll($threadsByHot->getQuery());
        return $threadsBySite;
    }

    private function getBaseThreadsBuilder($isDraft = Thread::BOOL_NO, $filterApprove = true)
    {
        $threads = Thread::query()
            ->select('th.*')
            ->from('threads as th')
            ->whereNull('th.deleted_at')
            ->whereNotNull('th.user_id')
            ->where('th.is_draft', $isDraft)
            ->where('th.is_display', Thread::BOOL_YES);
        if ($filterApprove) {
            $threads->where('th.is_approved', Thread::BOOL_YES);
        }
        return $threads;
    }

    /**
     * @desc 筛选变量
     * @param $filter
     * @return array
     */
    private function initFilter($filter)
    {
        empty($filter) && $filter = [];
        $this->dzqValidate($filter, [
            'essence' => 'integer|in:0,1',
            'types' => 'array',
            'sort' => 'integer|in:1,2,3,4',
            'attention' => 'integer|in:0,1',
            'complex' => 'integer|in:1,2,3,4,5',
            'exclusiveIds' => 'array',
            'categoryids' => 'array'
        ]);
        $essence = '';
        $types = [];
        $sort = Thread::SORT_BY_THREAD;
        $attention = 0;
        $search = '';
        $complex = '';
        isset($filter['essence']) && $essence = $filter['essence'];
        isset($filter['types']) && $types = $filter['types'];
        isset($filter['sort']) && $sort = $filter['sort'];
        isset($filter['attention']) && $attention = $filter['attention'];
        isset($filter['search']) && $search = $filter['search'];
        isset($filter['complex']) && $complex = $filter['complex'];
        isset($filter['exclusiveIds']) && $exclusiveIds = $filter['exclusiveIds'];
        $categoryids = $this->categoryIds;
        return [$essence, $types, $sort, $attention, $search, $complex, $categoryids];
    }
}
