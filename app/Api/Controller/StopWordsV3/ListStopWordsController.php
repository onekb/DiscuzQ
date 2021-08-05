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

namespace App\Api\Controller\StopWordsV3;

use App\Common\ResponseCode;
use App\Models\StopWord;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Support\Arr;

class ListStopWordsController extends DzqController
{

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有访问敏感词列表的权限');
        }
        return true;
    }

    public function main()
    {
        $currentPage = $this->inPut('page');
        $perPage = $this->inPut('perPage');
        $filter = $this->inPut('filter');
        $query = StopWord::query();
        if ($keyword = trim(Arr::get($filter, 'keyword'))) {
            $query = $query
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('find', 'like', "%$keyword%");
                });
        }
        $stopWords = $this->pagination($currentPage, $perPage, $query);
        return $this->outPut(ResponseCode::SUCCESS,'', $this->camelData($stopWords));
    }
}
