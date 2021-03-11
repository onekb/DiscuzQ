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

namespace App\Repositories;

use App\Common\CacheKey;
use App\Models\Sequence;
use App\Models\Thread;
use App\Models\Setting;
use Discuz\Foundation\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\TranslatorException;
use Illuminate\Contracts\Cache\Repository as Cache;

class SequenceRepository extends AbstractRepository
{
    protected $cache;

    public function __construct()
    {
        $this->cache = app('cache');
    }

    /**
     * @param mixed $index_thread_ids
     */
    public function getSequenceCache($page){
        $sequenceList = Sequence::query()->first();

        $query = Thread::query();

        if(!empty($sequenceList['category_ids'])){
            $query->orWhereIn('threads.category_id', explode(',', $sequenceList['category_ids']));
        }

        if(!empty($sequenceList['user_ids'])){
            $query->orWhereIn('threads.user_id', explode(',', $sequenceList['user_ids']));
        }

        if(!empty($sequenceList['group_ids'])){
            $query->leftJoin('group_user', 'threads.user_id', '=', 'group_user.user_id');
            $query->orWhereIn('group_user.group_id', explode(',', $sequenceList['group_ids']));
        }

        if(!empty($sequenceList['topic_ids'])){
            $query->leftJoin('thread_topic', 'threads.id', '=', 'thread_topic.thread_id');
            $query->orWhereIn('thread_topic.topic_id', explode(',', $sequenceList['topic_ids']));
        }

        if(!empty($sequenceList['thread_ids'])) {
            $query->orWhereIn('threads.id', explode(',', $sequenceList['thread_ids']));
        }

        if(!empty($sequenceList['block_user_ids'])) {
            $query->whereNotIn('threads.user_id', explode(',', $sequenceList['block_user_ids']));
        }

        if(!empty($sequenceList['block_thread_ids'])) {
            $query->whereNotIn('threads.id', explode(',', $sequenceList['block_thread_ids']));
        }

        if(!empty($sequenceList['block_topic_ids'])) {
            $query->whereNotIn('thread_topic.topic_id', explode(',', $sequenceList['block_topic_ids']));
        }

        $query->where('threads.is_approved', 1);
        $query->where('threads.is_draft', 0);
        $query->whereNull('threads.deleted_at');
        $query->whereNotNull('threads.user_id');
        $query->orderBy('threads.created_at', 'desc');
        $offset = ($page['number'] - 1) * $page['limit'];
        $index_thread_ids['thread_count'] = $query->count();
        $index_thread_ids['ids'] = $query->limit($page['limit'])->offset($offset)->pluck('id')->toArray();
        return $index_thread_ids;
    }

    /**
     * @param mixed $index_thread_ids
     */
    public function updateSequenceCache($id, $operation){
        if(empty($id)){
            return false;
        }

        $site_open_sort = Setting::query()->where('key', 'site_open_sort')->first();
        if($site_open_sort['value'] == 0){
            return false;
        }

        $sequenceList = Sequence::query()->first();

        $cacheKey = CacheKey::LIST_SEQUENCE_THREAD_INDEX;
        $index_thread_ids = $this->cache->get($cacheKey);
        $index_thread_ids = (array)$index_thread_ids;

        $thread = Thread::query()
            ->select('threads.id', 'threads.category_id', 'threads.user_id', 'group_user.group_id', 'thread_topic.topic_id')
            ->leftJoin('group_user', 'threads.user_id', '=', 'group_user.user_id')
            ->leftJoin('thread_topic', 'threads.id', '=', 'thread_topic.thread_id')
            ->where(['threads.id' => $id, 'threads.is_approved' => 1, 'threads.is_draft' => 0, 'threads.is_display' => 1])->whereNull('threads.deleted_at')->get()->toArray();
        $threadData = array();
        if(count($thread) >= 1){
            $threadData = array(
                'id'          => $thread[0]['id'],
                'category_id' => $thread[0]['category_id'],
                'user_id'     => $thread[0]['user_id'],
                'group_id'    => $thread[0]['group_id'],
                'topic_ids'   => array_column($thread, 'topic_id')
            );
        }

        if(empty($threadData)){
            if(!empty($index_thread_ids['ids']) && in_array($id, $index_thread_ids['ids'])){
                $index_thread_ids = array();
                $this->cache->put($cacheKey, $index_thread_ids, 1800);
                $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
            }
            return false;
        }

        if(!empty($sequenceList['block_thread_ids'])){
            $block_thread_ids = explode(',', $sequenceList['block_thread_ids']);
        }else{
            $block_thread_ids = array();
        }

        if(!empty($sequenceList['block_user_ids'])){
            $block_user_ids = explode(',', $sequenceList['block_user_ids']);
        }else{
            $block_user_ids = array();
        }

        if(!empty($sequenceList['block_topic_ids'])){
            $block_topic_ids = explode(',', $sequenceList['block_topic_ids']);
        }else{
            $block_topic_ids = array();
        }

        if(in_array($threadData['id'], $block_thread_ids) || in_array($threadData['user_id'], $block_user_ids) || in_array($threadData['topic_ids'], $block_topic_ids)) {
            if(!empty($index_thread_ids['ids']) && in_array($id, $index_thread_ids['ids'])){
                $index_thread_ids = array();
                $this->cache->put($cacheKey, $index_thread_ids, 1800);
                $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
            }
            return false;
        }

        if(!empty($sequenceList['thread_ids'])){
            $thread_ids = explode(',', $sequenceList['thread_ids']);
        }else{
            $thread_ids = array();
        }

        if(!empty($sequenceList['category_ids'])){
            $category_ids = explode(',', $sequenceList['category_ids']);
        }else{
            $category_ids = array();
        }

        if(!empty($sequenceList['user_ids'])){
            $user_ids = explode(',', $sequenceList['user_ids']);
        }else{
            $user_ids = array();
        }

        if(!empty($sequenceList['topic_ids'])){
            $topic_ids = explode(',', $sequenceList['topic_ids']);
        }else{
            $topic_ids = array();
        }

        if(!empty($sequenceList['group_ids'])){
            $group_ids = explode(',', $sequenceList['group_ids']);
        }else{
            $group_ids = array();
        }

        if(in_array($threadData['id'], $thread_ids) || in_array($threadData['category_id'], $category_ids) || 
            in_array($threadData['user_id'], $user_ids) || in_array($threadData['group_id'], $group_ids) || 
            in_array($threadData['topic_ids'], $topic_ids)) {
            if(!empty($index_thread_ids['ids'])){
                if(!in_array($id, $index_thread_ids['ids']) && $operation = 'add'){
                    $index_thread_ids['thread_count'] = $index_thread_ids['thread_count'] + 1;
                    $index_thread_ids['ids'] = array_merge((array)$id, $index_thread_ids);
                    $this->cache->put($cacheKey, $index_thread_ids, 1800);
                    $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
                    return true;
                }
            }else{
                $index_thread_ids['thread_count'] = 1;
                $index_thread_ids['ids'] = (array)$id;
                $this->cache->put($cacheKey, $index_thread_ids, 1800);
                $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
                return true;
            }
        }
    }

    /**
     *缓存key单独记录便于数据变更的时候清除缓存
     */
    public function appendCache($key, $value, $ttl)
    {
        $v0 = $this->cache->get($key);
        if (empty($v0)) {
            $v1 = [$value];
        } else {
            $v1 = json_decode($v0, true);
            if (!empty($v1)) {
                $v1[] = $value;
            } else {
                return false;
            }
        }
        $v1 = array_unique($v1);
        $this->cache->put($key, json_encode($v1, 256), $ttl);
        return $v1;
    }
}
