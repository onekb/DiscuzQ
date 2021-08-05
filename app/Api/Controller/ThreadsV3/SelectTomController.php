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


use App\Common\ResponseCode;
use App\Models\Thread;
use App\Models\ThreadTom;
use App\Modules\ThreadTom\TomTrait;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class SelectTomController extends DzqController
{
    use TomTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $thread = Thread::query()
            ->where('id', $this->inPut('threadId'))
            ->first();
        if (!$thread) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }
        return $userRepo->canViewThreadDetail($this->user, $thread);
    }

    public function main()
    {
        $threadId = $this->inPut('threadId');
        $tomId = $this->inPut('tomId');
        $key = $this->inPut('key');
        $tom = ThreadTom::query()->where([
            'thread_id' => $threadId,
            'tom_type' => $tomId,
            'key' => $key,
            'status' => ThreadTom::STATUS_ACTIVE
        ])->first();
        if (empty($tom)) {
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }
        $content = $this->buildTomJson($threadId, $tomId, $this->SELECT_FUNC, json_decode($tom->value, true));
        $result = $this->tomDispatcher([$key => $content]);
        $this->outPut(ResponseCode::SUCCESS, '', $result);
    }
}
