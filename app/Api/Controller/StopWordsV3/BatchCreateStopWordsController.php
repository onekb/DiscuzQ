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

use App\Commands\StopWord\BatchCreateStopWord;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class BatchCreateStopWordsController extends DzqController
{
    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有保存敏感词的权限');
        }
        return true;
    }

    public function main()
    {
        $overwrite = $this->inPut('overwrite');
        $words = $this->inPut('words');
        $data = [
            'type' =>'stop-words',
            'overwrite'=>$overwrite,
            'words'=>$words
        ];

        $result = $this->bus->dispatch(
            new BatchCreateStopWord($this->user, $data)
        );

        $data = [
            'type' => 'stop-words',
            'created' => $result->get('created', 0),    // 新建数量
            'updated' => $result->get('updated', 0),    // 修改数量
            'unique' => $result->get('unique', 0),      // 重复数量
        ];

        return $this->outPut(ResponseCode::SUCCESS,'', $data);
    }
}
