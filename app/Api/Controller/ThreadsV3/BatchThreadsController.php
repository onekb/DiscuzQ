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

use App\Commands\Thread\AdminBatchEditThreads;
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class BatchThreadsController extends DzqController
{

    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有批量修改主题的权限');
        }
        return true;
    }

    public function main()
    {
        $ids = $this->inPut("ids");
        if(empty($ids)){
            return $this->outPut(ResponseCode::INVALID_PARAMETER);
        }

        $actor = $this->user;

        $categoryId = $this->inPut("categoryId");
        $isApproved = $this->inPut("isApproved");
        $isSticky = $this->inPut("isSticky");
        $isEssence = $this->inPut("isEssence");
        $isDeleted = $this->inPut("isDeleted");
        $isFavorite = $this->inPut("isFavorite");
        $isSite = $this->inPut("isSite");

        $idsArr = explode(",", $ids);

        $requestData = [];
        foreach ($idsArr as $key=>$val){
            $requestData[$key]['type'] = "threads";
            $requestData[$key]['id'] = $val;
            $requestData[$key]['attributes'] = [];
            if(!empty($categoryId)){
                $requestData[$key]['relationships']['category']['data']['id'] = $categoryId;
            }
            if(!empty($isApproved) || $isApproved === 0){
                $requestData[$key]['attributes']['isApproved'] = $isApproved;
            }
            if(!empty($isSticky) || $isSticky === 0){
                $requestData[$key]['attributes']['isSticky'] = $isSticky;
            }
            if(!empty($isDeleted) || $isDeleted === 0){
                $requestData[$key]['attributes']['isDeleted'] = $isDeleted;
            }
            if(!empty($isFavorite) || $isFavorite === 0){
                $requestData[$key]['attributes']['isFavorite'] = $isFavorite;
            }
            if(!empty($isSite) || $isSite === 0){
                $requestData[$key]['attributes']['isSite'] = $isSite;
            }
            if(!empty($isEssence) || $isEssence === 0){
                $requestData[$key]['attributes']['isEssence'] = $isEssence;
            }
        }

        $result = $this->bus->dispatch(
            new AdminBatchEditThreads($actor, $requestData)
        );
        $result = $this->camelData($result);
        return $this->outPut(ResponseCode::SUCCESS,'', $result);
    }
    public function prefixClearCache($user)
    {
        $ids = $this->inPut('ids');
        if(empty($ids)){
            return $this->outPut(ResponseCode::INVALID_PARAMETER);
        }
        CacheKey::delListCache();
    }
}
