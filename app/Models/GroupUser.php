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

namespace App\Models;


use Discuz\Base\DzqModel;

class GroupUser extends DzqModel
{

    protected $table = 'group_user';

    public static $relationGroups = 'groups';


    public function groups(){
        return $this->hasOne(Group::class,'id','group_id');
    }

    public function getGroupInfo($userIds){
        return  GroupUser::query()->whereIn('user_id',$userIds)
            ->with(GroupUser::$relationGroups)->get()->toArray();
    }
}
