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
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class RecommendedUserListController extends DzqController
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
        $userCount = User::query()->where('status', 0)->count();
        if($userCount <= 10){
            $userList = User::query()
                ->select('id as userId', 'username', 'nickname')
                ->limit(10)
                ->where('status', 0)
                ->orderBy('updated_at')
                ->get()->toArray();
            return $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($userList));
        }

        $offset = rand(0,$userCount);
        if($offset > $userCount - 10){
            $offset = $userCount - 10;
        }
        $userId = User::query()->offset($offset)->where('status', 0)->limit(100)->pluck('id')->toArray();
        shuffle($userId);
        $ids = array_slice($userId,0,10);

        $userList = User::query()
            ->select('id as userId', 'username', 'nickname')
            ->whereIn('id', $ids)
            ->orderBy('updated_at')
            ->get()->toArray();
        return $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($userList));
    }
}
