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
use App\Models\Permission;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class TomPermissionsController extends DzqController
{

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            throw new PermissionDeniedException('访客没有权限访问帖子权限');
        }
        return true;
    }

    public function main()
    {
        $actor = $this->user;
        $actorGroup = $actor->getRelation("groups")->toArray();
        $actorId = $actorGroup[0]['id'];
        $query = GroupRepository::query();
        $groupData = $query->where('id',$actorId)->first();

        if(empty($groupData)){
            $this->outPut(ResponseCode::INVALID_PARAMETER);
        }

        $actorIds = array($actorId);
        $permissions = Permission::query()->whereIn('group_id', $actorIds)->get()->toArray();
        $group_permission = array_column($permissions, 'permission');
        $thread_permission = Permission::THREAD_PERMISSION;
        $result =[];

        $thread_permission_key = array_keys($thread_permission);

        if (!$actor->isAdmin()){
            foreach ($thread_permission_key as $k=>$tp){
                if (in_array($tp,$group_permission)){
                    $result[$tp]['enable'] = true;
                    $result[$tp]['desc'] = $thread_permission[$tp];
                }else{
                    $result[$tp]['enable'] = false;
                    $result[$tp]['desc'] = $thread_permission[$tp];
                }
            }
        }else{
            foreach ($thread_permission_key as $k=>$tp){
                $result[$tp]['enable'] = true;
                $result[$tp]['desc'] = $thread_permission[$tp];
            }
        }

        $newresult = [];
        foreach ($result as $k=>$value){
              $newk = substr($k,7);
              $newresult[$newk] = $value;
         }

        return $this->outPut(ResponseCode::SUCCESS, '',$newresult);
    }

}
