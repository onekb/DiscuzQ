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
use App\Common\ResponseCode;
use App\Models\Category;
use App\Models\Thread;
use App\Modules\ThreadTom\TomTrait;
use App\Repositories\UserRepository;
use App\Traits\ThreadNoticesTrait;
use Carbon\Carbon;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;

class DeleteThreadController extends DzqController
{
    use TomTrait;
    use ThreadNoticesTrait;

    private $thread;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $this->thread = Thread::query()
            ->where(['id' => $this->inPut('threadId')])
            ->whereNull('deleted_at')
            ->first();
        if (empty($this->thread)) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }
        return $userRepo->canHideThread($this->user, $this->thread);
    }

    public function main()
    {
        $thread = $this->thread;
        $thread->deleted_at = Carbon::now();
        $thread->deleted_user_id = $this->user->id;
        if (!$thread->save()) {
            $this->outPut(ResponseCode::DB_ERROR, '删除失败');
        }

        Category::refreshThreadCountV3($thread['category_id']);

        if ($thread->user_id != $this->user->id) {
            $this->threadNotices($thread, $this->user, 'isDeleted', $this->inPut('message'));
        }

        $this->outPut(ResponseCode::SUCCESS);
    }

    public function prefixClearCache($user)
    {
        CacheKey::delListCache();
        $threadId = $this->inPut('threadId');
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_THREADS, $threadId);
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_POSTS, $threadId);
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_TAGS, $threadId);
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_TOMS, $threadId);
    }
}
