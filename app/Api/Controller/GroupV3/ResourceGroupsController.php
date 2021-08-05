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

namespace App\Api\Controller\GroupV3;

use App\Common\ResponseCode;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Discuz\Auth\Exception\PermissionDeniedException;

class ResourceGroupsController extends DzqController
{
    public $optionalInclude = [
        'permission',
        'categoryPermissions',
    ];

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $id = $this->inPut('id');

        if(!$id){
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }

        $include = $this->inPut('include');
        $query = GroupRepository::query();
        $groupData = $query->where('id',$id)->first();
        if(empty($groupData)){
            $this->outPut(ResponseCode::INVALID_PARAMETER, 'ID为'.$id.'记录不存在');
        }
        $include = [$include];
        if (in_array('permission', $include)) {
            $query->with(['permission']);
        }

        $result = $this->camelData($query->where('id', $id)->first());


        return $this->outPut(ResponseCode::SUCCESS, '',$result);
    }
}
