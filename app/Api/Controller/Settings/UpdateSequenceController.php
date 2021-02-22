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

namespace App\Api\Controller\Settings;

use App\Common\CacheKey;
use App\Models\Setting;
use App\Models\Sequence;
use App\Models\Thread;
use App\Models\Permission;
use App\Models\User;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Http\DiscuzResponseFactory;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UpdateSequenceController implements RequestHandlerInterface
{
    use AssertPermissionTrait;

    protected $cache;

    public function __construct(User $actor, SettingsRepository $settings)
    {
        $this->cache = app('cache');
        $this->actor = $actor;
        $this->settings = $settings;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PermissionDeniedException
     * @throws Exception
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->assertAdmin($request->getAttribute('actor'));
        $cacheKey = CacheKey::LIST_SEQUENCE_THREAD_INDEX;

        $body = $request->getParsedBody();
        $data = Arr::get($body, 'data', []);
        $attributes = Arr::get($data, 'attributes', []);
        if(!isset($attributes['site_open_sort']) || empty($attributes['site_open_sort'])){
            $attributes['site_open_sort'] = 0;
        }

        $this->settings->set('site_open_sort', $attributes['site_open_sort'], 'default');

        $sequence = array(
            'category_ids' => $attributes['category_ids'] ?? '',
            'group_ids' => $attributes['group_ids'] ?? '',
            'user_ids' => $attributes['user_ids'] ?? '',
            'topic_ids' => $attributes['topic_ids'] ?? '',
            'thread_ids' => $attributes['thread_ids'] ?? '',
            'block_user_ids' => $attributes['block_user_ids'] ?? '',
            'block_topic_ids' => $attributes['block_topic_ids'] ?? '',
            'block_thread_ids' => $attributes['block_thread_ids'] ?? ''
        );
        Sequence::query()->delete();
        Sequence::query()->insert($sequence);

        if($attributes['site_open_sort'] == 0){
            $thread_ids = array();
            $this->cache->put($cacheKey, $thread_ids, 1800);
            $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);
            return DiscuzResponseFactory::EmptyResponse(204);
        }

        if(empty($attributes['category_ids']) && empty($attributes['group_ids']) && empty($attributes['user_ids']) && empty($attributes['topic_ids']) && empty($attributes['thread_ids'])){
            $ids = Thread::query()->where(['is_approved' => 1, 'is_draft' => 0])->whereNull('deleted_at')->pluck('id')->toArray();
        }else{
            if(!empty($attributes['category_ids'])) {
                $category_threadsList = Thread::query()->whereIn('category_id', explode(',', $attributes['category_ids']))->whereNull('deleted_at')->where('is_approved',1)->pluck('id')->toArray();
            }else{
                $category_threadsList = null;
            }

            if(!empty($attributes['group_ids'])) {
                $group_threadsList = Thread::query()->join('group_user', 'threads.user_id', '=', 'group_user.user_id')->whereIn('group_user.group_id', explode(',', $attributes['group_ids']))->whereNull('threads.deleted_at')->where('threads.is_approved',1)->pluck('id')->toArray();
            }else{
                $group_threadsList = null;
            }
            if(!empty($attributes['user_ids'])) {
                $user_threadsList = Thread::query()->whereIn('user_id', explode(',', $attributes['user_ids']))->whereNull('deleted_at')->where('is_approved',1)->pluck('id')->toArray();
            }else{
                $user_threadsList = null;
            }
            if(!empty($attributes['topic_ids'])) {
                $topic_threadsList = Thread::query()->join('thread_topic', 'threads.id', '=', 'thread_topic.thread_id')->whereIn('thread_topic.topic_id', explode(',', $attributes['topic_ids']))->whereNull('threads.deleted_at')->where('threads.is_approved',1)->pluck('id')->toArray();
            }else{
                $topic_threadsList = null;
            }

            if(!empty($attributes['thread_ids'])) {
                $threadsList = explode(',', $attributes['thread_ids']);
            }else{
                $threadsList = null;
            }

            $ids = array_keys(array_flip((array)$category_threadsList) + array_flip((array)$group_threadsList) + array_flip((array)$user_threadsList) + array_flip((array)$topic_threadsList) + array_flip((array)$threadsList));
        }

        if(!empty($attributes['block_user_ids'])) {
            $block_user_threadsList = Thread::query()->whereIn('user_id', explode(',', $attributes['block_user_ids']))->whereNull('deleted_at')->where('is_approved',1)->pluck('id')->toArray();
        }else{
            $block_user_threadsList = null;
        }
        if(!empty($attributes['block_topic_ids'])) {
            $block_topic_threadsList = Thread::query()->join('thread_topic', 'threads.id', '=', 'thread_topic.thread_id')->whereIn('thread_topic.topic_id', explode(',', $attributes['block_topic_ids']))->whereNull('threads.deleted_at')->where('threads.is_approved',1)->pluck('id')->toArray();
        }else{
            $block_topic_threadsList = null;
        }

        if(!empty($attributes['block_thread_ids'])) {
            $block_threadsList = explode(',', $attributes['block_thread_ids']);
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
        $this->cache->put($cacheKey, $thread_ids, 1800);
        $this->appendCache(CacheKey::LIST_SEQUENCE_THREAD_INDEX_KEYS, $cacheKey, 1800);

        return DiscuzResponseFactory::EmptyResponse(204);
    }

    /**
     *缓存key单独记录便于数据变更的时候清除缓存
     */
    private function appendCache($key, $value, $ttl)
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
