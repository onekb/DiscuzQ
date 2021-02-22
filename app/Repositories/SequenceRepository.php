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
    public function getSequenceCache(){
        $sequenceList = Sequence::query()->first();
        if(!isset($sequenceList) || empty($sequenceList)) {
            return false;
        }

        if(!empty($sequenceList['category_ids'])) {
            $category_threadsList = Thread::query()->whereIn('category_id', explode(',', $sequenceList['category_ids']))->whereNull('deleted_at')->where('is_approved',1)->pluck('id')->toArray();
        }else{
            $category_threadsList = null;
        }
        if(!empty($sequenceList['group_ids'])) {
            $group_threadsList = Thread::query()->join('group_user', 'threads.user_id', '=', 'group_user.user_id')->whereIn('group_user.group_id', explode(',', $sequenceList['group_ids']))->whereNull('threads.deleted_at')->where('threads.is_approved',1)->pluck('id')->toArray();
        }else{
            $group_threadsList = null;
        }
        if(!empty($sequenceList['user_ids'])) {
            $user_threadsList = Thread::query()->whereIn('user_id', explode(',', $sequenceList['user_ids']))->whereNull('deleted_at')->where('is_approved',1)->pluck('id')->toArray();
        }else{
            $user_threadsList = null;
        }
        if(!empty($sequenceList['topic_ids'])) {
            $topic_threadsList = Thread::query()->join('thread_topic', 'threads.id', '=', 'thread_topic.thread_id')->whereIn('thread_topic.topic_id', explode(',', $sequenceList['topic_ids']))->whereNull('threads.deleted_at')->where('threads.is_approved',1)->pluck('id')->toArray();
        }else{
            $topic_threadsList = null;
        }

        if(!empty($sequenceList['thread_ids'])) {
            $threadsList = explode(',', $sequenceList['thread_ids']);
        }else{
            $threadsList = null;
        }

        $ids = array_keys(array_flip((array)$category_threadsList) + array_flip((array)$group_threadsList) + array_flip((array)$user_threadsList) + array_flip((array)$topic_threadsList) + array_flip((array)$threadsList));

        if(empty($ids)){
            $ids = Thread::query()->where(['is_approved' => 1, 'is_draft' => 0])->whereNull('deleted_at')->pluck('id')->toArray();
        }

        if(!empty($sequenceList['block_user_ids'])) {
            $block_user_threadsList = Thread::query()->whereIn('user_id', explode(',', $sequenceList['block_user_ids']))->whereNull('deleted_at')->where('is_approved',1)->pluck('id')->toArray();
        }else{
            $block_user_threadsList = null;
        }
        if(!empty($sequenceList['block_topic_ids'])) {
            $block_topic_threadsList = Thread::query()->join('thread_topic', 'threads.id', '=', 'thread_topic.thread_id')->whereIn('thread_topic.topic_id', explode(',', $sequenceList['block_topic_ids']))->whereNull('threads.deleted_at')->where('threads.is_approved',1)->pluck('id')->toArray();
        }else{
            $block_topic_threadsList = null;
        }

        if(!empty($sequenceList['block_thread_ids'])) {
            $block_threadsList = explode(',', $sequenceList['block_thread_ids']);
        }else{
            $block_threadsList = null;
        }

        $block_ids = array_keys(array_flip((array)$block_user_threadsList) + array_flip((array)$block_topic_threadsList) + array_flip((array)$block_threadsList));

        $thread_ids = array();
        if(isset($ids) && !empty($ids)) {
            foreach ($ids as $key => $val) {
                $thread_ids[$val] = $val;
            }
        }

        if(isset($block_ids) && !empty($block_ids)){
            foreach ($block_ids as $key => $value) {
                if(isset($thread_ids[$value])){
                    unset($thread_ids[$value]);
                }
            }
        }

        $thread_ids = array_merge($thread_ids);
        $cacheKey = CacheKey::LIST_SEQUENCE_THREAD_INDEX;
        $this->cache->put($cacheKey, $thread_ids, 1800);
        $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
        return $thread_ids;
    }

    /**
     * @param mixed $index_thread_ids
     */
    public function updateSequenceCache($id){

        $site_open_sort = Setting::query()->where('key', 'site_open_sort')->first();
        if($site_open_sort['value'] == 0){
            return false;
        }

        $sequenceList = Sequence::query()->first();
        if(!isset($sequenceList) || empty($sequenceList)) {
            return false;
        }

        $thread = Thread::query()->select('threads.*')
            ->where(['threads.id' => $id, 'threads.is_approved' => 1])->whereNull('threads.deleted_at')->first();

        $cacheKey = CacheKey::LIST_SEQUENCE_THREAD_INDEX;
        $index_thread_ids = $this->cache->get($cacheKey);
        $index_thread_ids = (array)$index_thread_ids;

        if(!isset($thread) || empty($thread)) {
            if(isset($index_thread_ids) && !empty($index_thread_ids)) {
                if(in_array($id, $index_thread_ids)) {
                    foreach ($index_thread_ids as $key => $value) {
                        if($value == $id){
                            unset($index_thread_ids[$key]);
                        }
                    }
                    $index_thread_ids = array_merge($index_thread_ids);
                    $this->cache->put($cacheKey, $index_thread_ids, 1800);
                    $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }

        $topic_id = Thread::query()->select('thread_topic.topic_id')->join('thread_topic', 'threads.id', '=', 'thread_topic.thread_id')->where(['threads.id' => $id])->get()->toArray();

        $group_id = Thread::query()->select('group_user.group_id')->join('group_user', 'threads.user_id', '=', 'group_user.user_id')->where(['threads.id' => $id])->first();

        $block_thread_ids = explode(',',$sequenceList['block_thread_ids']);
        $block_user_ids = explode(',',$sequenceList['block_user_ids']);
        $block_topic_ids = explode(',',$sequenceList['block_topic_ids']);
        if(in_array($id, $block_thread_ids) || in_array($thread['user_id'], $block_user_ids) || in_array($topic_id, $block_topic_ids)) {
            if(isset($index_thread_ids) && !empty($index_thread_ids)) {
                if(in_array($id, $index_thread_ids)) {
                    foreach ($index_thread_ids as $key => $value) {
                        if($value == $id){
                            unset($index_thread_ids[$key]);
                        }
                    }
                    $index_thread_ids = array_merge($index_thread_ids);
                    $this->cache->put($cacheKey, $index_thread_ids, 1800);
                    $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }

        if(empty($sequenceList['category_ids']) && empty($sequenceList['group_ids']) && empty($sequenceList['user_ids']) && empty($sequenceList['topic_ids']) && empty($sequenceList['thread_ids'])){
            $index_thread_ids = Thread::query()->where(['is_approved' => 1, 'is_draft' => 0])->whereNull('deleted_at')->pluck('id')->toArray();
            $index_thread_ids = array_merge($index_thread_ids, (array)$id);
            $this->cache->put($cacheKey, $index_thread_ids, 1800);
            $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
            return true;
        }

        $thread_ids = explode(',',$sequenceList['thread_ids']);
        $category_ids = explode(',',$sequenceList['category_ids']);
        $user_ids = explode(',',$sequenceList['user_ids']);
        $topic_ids = explode(',',$sequenceList['topic_ids']);
        $group_ids = explode(',',$sequenceList['group_ids']);
        if(in_array($id, $thread_ids) || in_array($thread['category_id'], $category_ids) ||
            in_array($thread['user_id'], $user_ids) || in_array($group_id['group_id'], $group_ids)) {
            if(isset($index_thread_ids) && !empty($index_thread_ids)) {
                if(!in_array($id, $index_thread_ids)) {
                    $index_thread_ids = array_merge($index_thread_ids,(array)$id);
                    $this->cache->put($cacheKey, $index_thread_ids, 1800);
                    $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
                    return true;
                }else{
                    return false;
                }
            }else{
                $index_thread_ids = (array)$id;
                $this->cache->put($cacheKey, $index_thread_ids, 1800);
                $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
                return true;
            }
        }

        if(isset($topic_id) && !empty($topic_id) && count($topic_id) > 1){
            foreach ($topic_id as $key => $value) {
                if(in_array($value, $topic_ids) && !in_array($id, $index_thread_ids)) {
                    $index_thread_ids = array_merge($index_thread_ids,(array)$id);
                    $this->cache->put($cacheKey, $index_thread_ids, 1800);
                    $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
                    return true;
                }
            }
        }else{
            return false;
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
