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

namespace App\Listeners\Category;

use App\Events\Category\Created;
use App\Models\Group;
use App\Models\Permission;
use Illuminate\Contracts\Events\Dispatcher;

class CategoryListener
{
    public function subscribe(Dispatcher $events)
    {
        // 添加分类时，设置分类下的权限
        $events->listen(Created::class, [$this, 'whenCategoryCreated']);
    }

    /**
     * @param Created $event
     */
    public function whenCategoryCreated(Created $event)
    {
        $category = $event->category;

        $blockIds = [Group::ADMINISTRATOR_ID, Group::UNPAID];
        $groupIds = Group::query()->whereNotIn('id', $blockIds)->pluck('id');
        $ids = $groupIds->toArray();
        $oldpPermissions = Permission::query()->whereIn('group_id', $ids)->get()->toArray();
        $groupPermissions = [];
        foreach ($oldpPermissions as $key => $value) {
            if(isset($groupPermissions[$value['group_id']])){
                $groupPermissions[$value['group_id']][] = $value['permission'];
            }else{
                $groupPermissions[$value['group_id']][] = $value['permission'];
            }
        }

        $newPermissions = [];
        foreach ($ids as $key => $value) {
            if($value !== Group::GUEST_ID){
                if(!in_array('createThread', $groupPermissions[$value])){
                    $newPermissions[] = [
                        'group_id' => $value,
                        'permission' => "category{$category->id}.createThread",
                    ];
                }

                if(!in_array('viewThreads', $groupPermissions[$value])){
                    $newPermissions[] = [
                        'group_id' => $value,
                        'permission' => "category{$category->id}.viewThreads",
                    ];
                }

                if(!in_array('thread.reply', $groupPermissions[$value])){
                    $newPermissions[] = [
                        'group_id' => $value,
                        'permission' => "category{$category->id}.thread.reply",
                    ];
                }

                if(!in_array('thread.viewPosts', $groupPermissions[$value])){
                    $newPermissions[] = [
                        'group_id' => $value,
                        'permission' => "category{$category->id}.thread.viewPosts",
                    ];
                }
            }else{
                if(!in_array('viewThreads', $groupPermissions[Group::GUEST_ID])){
                    $newPermissions[] = [
                        'group_id' => Group::GUEST_ID,
                        'permission' => "category{$category->id}.viewThreads",
                    ];
                }

                if(!in_array('thread.viewPosts', $groupPermissions[Group::GUEST_ID])){
                    $newPermissions[] = [
                        'group_id' => Group::GUEST_ID,
                        'permission' => "category{$category->id}.thread.viewPosts",
                    ];
                }
            }
        }

        if(!empty($newPermissions)){
            Permission::query()->insert($newPermissions);
        }
    }
}
