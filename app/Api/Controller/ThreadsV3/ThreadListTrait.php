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

use App\Common\CacheKey;
use App\Models\Category;
use App\Models\Permission;
use App\Models\PostUser;
use App\Models\ThreadUser;
use Discuz\Base\DzqCache;
use App\Models\Attachment;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Order;
use App\Models\Post;
use App\Models\Thread;
use App\Models\ThreadTag;
use App\Models\ThreadTom;
use App\Models\ThreadVideo;
use App\Models\User;
use App\Modules\ThreadTom\TomConfig;

trait ThreadListTrait
{
    private function getFullThreadData($threads, $isList = false)
    {
        $loginUserId = $this->user->id;
        $userIds = array_unique(array_column($threads, 'user_id'));
        $groupUsers = $this->getGroupUserInfo($userIds);
        $users = DzqCache::hMGet(CacheKey::LIST_THREADS_V3_USERS, $userIds, function ($userIds) {
            return User::instance()->getUsers($userIds);
        }, 'id');
        $threadIds = array_column($threads, 'id');
        $posts = DzqCache::hMGet(CacheKey::LIST_THREADS_V3_POSTS, $threadIds, function ($threadIds) {
            return Post::instance()->getPosts($threadIds, false, false);
        }, 'thread_id');
        $postIds = array_column($posts, 'id');
        $toms = DzqCache::hMGet(CacheKey::LIST_THREADS_V3_TOMS, $threadIds, function ($threadIds) {
            return ThreadTom::query()->whereIn('thread_id', $threadIds)->where('status', ThreadTom::STATUS_ACTIVE)->get()->toArray();
        }, 'thread_id', true);

        $tags = DzqCache::hMGet(CacheKey::LIST_THREADS_V3_TAGS, $threadIds, function ($threadIds) {
            return ThreadTag::query()->whereIn('thread_id', $threadIds)->get()->toArray();
        }, 'thread_id', true);
        $this->setGlobalCache();
        $inPutToms = $this->buildIPutToms($toms);
        $result = [];
        $concatString = '';
        $loginUserData = $this->getLoginUserData($loginUserId, $threadIds, $postIds);
        foreach ($threads as $thread) {
            $threadId = $thread['id'];
            $userId = $thread['user_id'];
            $user = empty($users[$userId]) ? false : $users[$userId];
            $groupUser = empty($groupUsers[$userId]) ? false : $groupUsers[$userId];
            $post = empty($posts[$threadId]) ? false : $posts[$threadId];
            if ($post == false) {
                continue;
            }
            $tomInput = empty($inPutToms[$threadId]) ? false : $inPutToms[$threadId];
            $threadTags = [];
            isset($tags[$threadId]) && $threadTags = $tags[$threadId];
            $concatString .= ($thread['title'] . $post['content']);
            $result[] = $this->packThreadDetail($user, $groupUser, $thread, $post, $tomInput, false, $threadTags, $loginUserData);
        }
        list($searches, $replaces) = ThreadHelper::getThreadSearchReplace($concatString);
        foreach ($result as &$item) {
            $item['title'] = str_replace($searches, $replaces, $item['title']);
            $text = str_replace($searches, $replaces, $item['content']['text']);
            if ($isList) {
                $maxText = 5000;
                //todo 等待前端解决表情导致文字内容过长的问题
//                if (mb_strlen($text) > $maxText) {
//                    $text = mb_substr($text, 0, $maxText) . '...';
//                }
            }
            $item['content']['text'] = $text;
        }
        return $result;
    }

    private function getLoginUserData($loginUserId, $threadIds, $postIds)
    {
        //付费订单
        $payOrders = [];
        //打赏订单
        $rewardOrders = [];
        Order::query()
            ->whereIn('thread_id', $threadIds)
            ->whereIn('type', [Order::ORDER_TYPE_THREAD, Order::ORDER_TYPE_ATTACHMENT, Order::ORDER_TYPE_REWARD])
            ->where([
                'user_id' => $loginUserId,
                'status' => Order::ORDER_STATUS_PAID
            ])->get()->each(function ($item) use (&$rewardOrders, &$payOrders) {
                $item = $item->toArray();
                if ($item['type'] == Order::ORDER_TYPE_REWARD) {
                    $rewardOrders[$item['thread_id']] = $item;
                } else {
                    $payOrders[$item['thread_id']] = $item;
                }
            });
        //我的点赞
        $postUsers = PostUser::query()->whereIn('post_id', $postIds)->where('user_id', $loginUserId)->get()->keyBy('post_id')->toArray();
        //我的收藏
        $threadUsers = ThreadUser::query()->whereIn('thread_id', $threadIds)->where('user_id', $loginUserId)->get()->keyBy('thread_id')->toArray();
        return [
            ThreadHelper::EXIST_PAY_ORDERS => $payOrders,
            ThreadHelper::EXIST_REWARD_ORDERS => $rewardOrders,
            ThreadHelper::EXIST_POST_USERS => $postUsers,
            ThreadHelper::EXIST_THREAD_USERS => $threadUsers
        ];
    }

    private function setGlobalCache()
    {
        $cache = [
            CacheKey::LIST_THREADS_V3_POST_USERS => DzqCache::get(CacheKey::LIST_THREADS_V3_POST_USERS),
            CacheKey::LIST_THREADS_V3_ATTACHMENT => DzqCache::get(CacheKey::LIST_THREADS_V3_ATTACHMENT),
            CacheKey::LIST_THREADS_V3_THREADS => DzqCache::get(CacheKey::LIST_THREADS_V3_THREADS),
            CacheKey::LIST_THREADS_V3_VIDEO => DzqCache::get(CacheKey::LIST_THREADS_V3_VIDEO),

//            CacheKey::LIST_THREADS_V3_USER_PAY_ORDERS . $loginUserId => DzqCache::get(CacheKey::LIST_THREADS_V3_USER_PAY_ORDERS . $loginUserId),
//            CacheKey::LIST_THREADS_V3_THREAD_USERS . $loginUserId => DzqCache::get(CacheKey::LIST_THREADS_V3_THREAD_USERS . $loginUserId),
//            CacheKey::LIST_THREADS_V3_POST_LIKED . $loginUserId => DzqCache::get(CacheKey::LIST_THREADS_V3_POST_LIKED . $loginUserId),
//            CacheKey::LIST_THREADS_V3_USER_REWARD_ORDERS . $loginUserId => DzqCache::get(CacheKey::LIST_THREADS_V3_USER_REWARD_ORDERS . $loginUserId)
        ];
        app()->instance(CacheKey::APP_CACHE, $cache);
    }

    private function getGroupUserInfo($userIds)
    {
        $groups = array_column(Group::getGroups(), null, 'id');
        $groupUsers = DzqCache::hMGet(CacheKey::LIST_THREADS_V3_GROUP_USER, $userIds, function ($userIds) {
            return GroupUser::query()->whereIn('user_id', $userIds)->get()->toArray();
        }, 'user_id');
        foreach ($groupUsers as &$groupUser) {
            $groupUser['groups'] = $groups[$groupUser['group_id']];
        }
        return $groupUsers;
    }

    /**
     * @desc 未查询到的数据添加默认空值
     * @param $ids
     * @param $array
     * @param null $value
     * @return mixed
     */
    private function appendDefaultEmpty($ids, &$array, $value = null)
    {
        foreach ($ids as $id) {
            if (!isset($array[$id])) {
                $array[$id] = $value;
            }
        }
        return $array;
    }

    /**
     * @desc 公共数据
     * @param $threadsList
     */
    private function initDzqGlobalData($threadsList)
    {
        $threads = $this->getAllThreadsList($threadsList);
        $threadIds = array_column($threads, 'id');
        $posts = $this->cachePosts($threadIds);
        $postIds = array_column($posts, 'id');
        $userIds = array_unique(array_column($threads, 'user_id'));
        $this->cacheThreads($threads);
        $this->cacheUsers($userIds);
        $this->cacheGroupUser($userIds);
        $this->cacheTags($threadIds);
        $toms = $this->cacheToms($threadIds);
        $attachmentIds = [];
        $threadVideoIds = [];
        $this->buildIPutToms($toms, $attachmentIds, $threadVideoIds, true);
        $this->cacheAttachment($attachmentIds);
        $this->cacheVideo($threadVideoIds);
        $this->cachePostUsers($threadIds, $postIds, $posts);
        $posts = array_column($posts, null, 'thread_id');
        $this->cacheSearchReplace($threads, $posts);
    }

    /**
     * @desc 当前登录用户个性化数据
     * @param $loginUserId
     * @param $cacheKey
     * @param $filterKey
     * @param $preloadCount
     */
    private function initDzqUserData($loginUserId, $cacheKey, $filterKey, $preloadCount)
    {
        $data = DzqCache::hGet($cacheKey, $filterKey);
        if (!$data) {
            return;
        }
        $pages = array_column($data, 'pageData');
        $threadIds = [];
        foreach ($pages as $ids) {
            $threadIds = array_merge($threadIds, $ids);
        }
        $this->cacheUserOrders($loginUserId, $threadIds, $preloadCount);
        $this->cachePostLikedAndFavor($loginUserId, $threadIds, $preloadCount);
    }

    private function getAllThreadsList($threadsByPage)
    {
        $threads = [];
        foreach ($threadsByPage as $listItems) {
            $pageData = $listItems['pageData'];
            foreach ($pageData as $thread) {
                $threads[] = $thread;
            }
        }
        return $threads;
    }

    private function buildIPutToms($tomData, &$attachmentIds = [], &$threadVideoIds = [], $withIds = false)
    {
        $inPutToms = [];
        foreach ($tomData as $threadId => $toms) {
            foreach ($toms as $tom) {
                $value = json_decode($tom['value'], true);
                if ($withIds) {
                    switch ($tom['tom_type']) {
                        case TomConfig::TOM_IMAGE:
                            isset($value['imageIds']) && $attachmentIds = array_merge($attachmentIds, $value['imageIds']);
                            break;
                        case TomConfig::TOM_DOC:
                            isset($value['docIds']) && $attachmentIds = array_merge($attachmentIds, $value['docIds']);
                            break;
                        case TomConfig::TOM_VIDEO:
                            isset($value['videoId']) && $threadVideoIds[] = $value['videoId'];
                            break;
                        case TomConfig::TOM_AUDIO:
                            isset($value['audioId']) && $threadVideoIds[] = $value['audioId'];
                            break;
                    }
                }
                $inPutToms[$tom['thread_id']][$tom['key']] = $this->buildTomJson($tom['thread_id'], $tom['tom_type'], $this->SELECT_FUNC, $value);
            }
        }
        if ($withIds) {
            $attachmentIds = array_values(array_unique($attachmentIds));
            $threadVideoIds = array_values(array_unique($threadVideoIds));
        }
        return $inPutToms;
    }

    private function cacheUsers($userIds)
    {
        $users = User::instance()->getUsers($userIds);
        DzqCache::hMSet(CacheKey::LIST_THREADS_V3_USERS, $users, 'id');
        return $users;
    }

    private function cacheGroupUser($userIds)
    {
        $groupUsers = GroupUser::query()->whereIn('user_id', $userIds)->get()->toArray();
        DzqCache::hMSet(CacheKey::LIST_THREADS_V3_GROUP_USER, $groupUsers, 'user_id');
        return $groupUsers;
    }

    private function cacheTags($threadIds)
    {
        $tags = ThreadTag::query()->whereIn('thread_id', $threadIds)->get()->toArray();
        DzqCache::hMSet(CacheKey::LIST_THREADS_V3_TAGS, $tags, 'thread_id', true, $threadIds);
    }

    private function cacheThreads($threads)
    {
        DzqCache::hMSet(CacheKey::LIST_THREADS_V3_THREADS, $threads, 'id');
        return $threads;
    }

    private function cachePosts($threadIds)
    {
        $posts = Post::instance()->getPosts($threadIds);
        DzqCache::hMSet(CacheKey::LIST_THREADS_V3_POSTS, $posts, 'thread_id');
        return $posts;
    }

    private function cacheToms($threadIds)
    {
        $toms = ThreadTom::query()->whereIn('thread_id', $threadIds)->where('status', ThreadTom::STATUS_ACTIVE)->get()->toArray();
        $toms = DzqCache::hMSet(CacheKey::LIST_THREADS_V3_TOMS, $toms, 'thread_id', true, $threadIds);
        return $toms;
    }

    private function cacheSearchReplace($threads, $posts)
    {
        $linkString = '';
        foreach ($threads as $thread) {
            $threadId = $thread['id'];
            $post = $posts[$threadId] ?? '';
            $linkString .= ($thread['title'] . (!empty($post) ? $post['content'] : ''));
        }
        $sReplaces = Thread::instance()->getReplaceStringV3($linkString);
        DzqCache::hMSet(CacheKey::LIST_THREADS_V3_SEARCH_REPLACE, $sReplaces);
        return $sReplaces;
    }

    private function cacheAttachment($attachmentIds)
    {
        //todo 附件集合对象改成数组对象
        $attachments = Attachment::query()->whereIn('id', $attachmentIds)->get()->keyBy('id');
        $attachments = $this->appendDefaultEmpty($attachmentIds, $attachments, null);
        app('cache')->put(CacheKey::LIST_THREADS_V3_ATTACHMENT, $attachments);
        return $attachments;
    }

    private function cacheVideo($threadVideoIds)
    {
        $threadVideos = ThreadVideo::query()->whereIn('id', $threadVideoIds)->get()->toArray();
        $threadVideos = DzqCache::hMSet(CacheKey::LIST_THREADS_V3_VIDEO, $threadVideos, 'id', false, $threadVideoIds, null);
        return $threadVideos;
    }


    private function cachePostUsers($threadIds, $postIds, $posts)
    {
        $likedUsers = ThreadHelper::getThreadLikedDetail($threadIds, $postIds, $posts);
        DzqCache::hMSet(CacheKey::LIST_THREADS_V3_POST_USERS, $likedUsers);
        return $likedUsers;
    }

    private function cacheUserOrders($userId, $threadIds, $preloadCount)
    {
        $key1 = CacheKey::LIST_THREADS_V3_USER_PAY_ORDERS . $userId;
        $key2 = CacheKey::LIST_THREADS_V3_USER_REWARD_ORDERS . $userId;
//        $d1 = DzqCache::get($key1);
//        $d2 = DzqCache::get($key2);
//        if (is_array($d1) && count($d1) >= $preloadCount && is_array($d2) && count($d1) >= $preloadCount) {
//            return;
//        }
        $orders = Order::query()
            ->where([
                'user_id' => $userId,
                'status' => Order::ORDER_STATUS_PAID
            ])->whereIn('type', [Order::ORDER_TYPE_THREAD, Order::ORDER_TYPE_ATTACHMENT, Order::ORDER_TYPE_REWARD])
            ->whereIn('thread_id', $threadIds)->get()->toArray();
        $orderPay = [];
        $orderReward = [];
        foreach ($orders as $order) {
            if ($order['type'] == Order::ORDER_TYPE_THREAD || $order['type'] == Order::ORDER_TYPE_ATTACHMENT) {
                $orderPay[] = $order;
            } else if ($order['type'] == Order::ORDER_TYPE_REWARD) {
                $orderReward[] = $order;
            }
        }
        DzqCache::hMSet($key1, $orderPay, 'thread_id', false, $threadIds, null);
        DzqCache::hMSet($key2, $orderReward, 'thread_id', false, $threadIds, null);
    }

    //点赞收藏
    private function cachePostLikedAndFavor($userId, $threadIds, $preloadCount)
    {
        $key1 = CacheKey::LIST_THREADS_V3_POST_LIKED . $userId;
        $key2 = CacheKey::LIST_THREADS_V3_THREAD_USERS . $userId;
//        $d1 = DzqCache::get($key1);
//        $d2 = DzqCache::get($key2);
//        if (is_array($d1) && count($d1) >= $preloadCount && is_array($d2) && count($d1) >= $preloadCount) {
//            return;
//        }
        $posts = Post::instance()->getPosts($threadIds);
        $postIds = array_column($posts, 'id');
        $postUsers = PostUser::query()->where('user_id', $userId)->whereIn('post_id', $postIds)->get()->toArray();
        $favorite = ThreadUser::query()->whereIn('thread_id', $threadIds)->where('user_id', $userId)->get()->toArray();
        DzqCache::hMSet($key1, $postUsers, 'post_id', false, $postIds, null);
        DzqCache::hMSet($key2, $favorite, 'thread_id', false, $threadIds, null);
    }
}
