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

namespace App\Api\Controller\Recommend;

use App\Common\ResponseCode;
use App\Models\Topic;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class RecommendedTopicListController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有获取推荐信息的权限!');
        }
        return true;
    }

    public function main()
    {
        $topicCount = Topic::query()->count();
        if($topicCount <= 10){
            $topicList = Topic::query()
                ->select('id as topicId', 'content as topicTitle')
                ->limit(10)
                ->orderBy('updated_at')
                ->get()->toArray();
            return $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($topicList));
        }

        $offset = rand(0, $topicCount);

        if($offset > $topicCount - 10){
            $offset = $topicCount - 10;
        }

        $topicId = Topic::query()->offset($offset)->limit(100)->pluck('id')->toArray();
        shuffle($topicId);
        $ids = array_slice($topicId, 0, 10);

        $topicList = Topic::query()
            ->select('id as topicId', 'content as topicTitle')
            ->whereIn('id', $ids)
            ->orderBy('updated_at')
            ->get()->toArray();
        return $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($topicList));
    }
}
