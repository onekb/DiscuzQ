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

namespace App\Api\Controller\Category;

use App\Common\ResponseCode;
use App\Models\Category;
use App\Models\Permission;
use Discuz\Base\DzqController;

class ListCategoriesV2Controller extends DzqController
{
    public function main()
    {
        $groups = $this->user->groups->toArray();
        $groupIds = array_column($groups, 'id');
        $permissions = Permission::query()->whereIn('group_id', $groupIds)->get()->toArray();
        $permissions = array_column($permissions, 'permission');

        $categories = Category::query()
            ->select([
                'id as pid', 'name', 'description', 'icon', 'sort', 'property', 'thread_count as threadCount', 'parentid'
            ])
            ->orderBy('parentid', 'asc')
            ->orderBy('sort')
            ->get()->toArray();

        $categoriesFather = [];
        $categoriesChild = [];

        foreach ($categories as $category) {
            $createThreadPermission = 'category' . $category['pid'] . '.createThread';
            // 全局或单个分类创建权限
            if (in_array('createThread', $permissions) || in_array($createThreadPermission, $permissions) || $this->user->isAdmin()) {
                $category['canCreateThread'] = true;
            } else {
                $category['canCreateThread'] = false;
            }

            $category['searchIds'] = (int)$category['pid'];

            // 二级子类集合
            if ($category['parentid'] !== 0) {
                $categoriesChild[$category['parentid']][] = $category;
            }

            // 一级分类 --- 全局或单个分类查看权限
            $viewPermission = 'category' . $category['pid'] . '.viewThreads';
            if ($category['parentid'] == 0 && (in_array('viewThreads', $permissions) || in_array($viewPermission, $permissions) || $this->user->isAdmin())) {
                $categoriesFather[] = $category;
            }
        }
        // 获取一级分类的二级子类
        foreach ($categoriesFather as $key => $value) {
            if (isset($categoriesChild[$value['pid']])) {
                $categoriesFather[$key]['searchIds'] = array_merge([$value['searchIds']], array_column($categoriesChild[$value['pid']], 'pid'));
                $categoriesFather[$key]['children'] = $categoriesChild[$value['pid']];
            } else {
                $categoriesFather[$key]['children'] = [];
            }
        }
        $this->outPut(ResponseCode::SUCCESS, '', $categoriesFather);
    }
}
