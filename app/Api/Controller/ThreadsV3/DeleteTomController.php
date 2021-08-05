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
use App\Models\Thread;
use App\Models\ThreadTom;
use App\Modules\ThreadTom\TomTrait;
use App\Repositories\UserRepository;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;

class DeleteTomController extends DzqController
{
    use TomTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $thread = Thread::getOneActiveThread($this->inPut('threadId'));
        if (!$thread) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }

        return $userRepo->canEditThread($this->user, $thread);
    }

    public function main()
    {
        $threadId = $this->inPut('threadId');
        $tomType = $this->inPut('tomId');
        $key = $this->inPut('key');
        $tom = ThreadTom::query()->where([
            'thread_id' => $threadId,
            'tom_type' => $tomType,
            'key' => $key,
            'status' => ThreadTom::STATUS_ACTIVE
        ])->first();
        if (empty($tom)) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }
        $tom->status = ThreadTom::STATUS_DELETE;
        if (!$tom->save()) {
            $this->outPut(ResponseCode::DB_ERROR);
        }
        $this->outPut(ResponseCode::SUCCESS);
    }

    public function prefixClearCache($user)
    {
        $threadId = $this->inPut('threadId');
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_TOMS, $threadId);
    }
}
