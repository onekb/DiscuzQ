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

namespace App\Api\Controller\CategoryV3;

use App\Common\ResponseCode;
use App\Models\Category;
use App\Models\Thread;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class ListCategoriesThreadController extends DzqController
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            throw new PermissionDeniedException();
        }
        return true;
    }

    public function main()
    {
        $threadId = $this->inPut('threadId') ?? 0;
        if (!empty($threadId)) {
            $threadData = Thread::query()
                ->where('id', $threadId)
                ->whereNull('deleted_at')
                ->where('is_approved', Thread::BOOL_YES)
                ->first();
        }

        $categories = Category::query()
            ->select([
                'id as pid', 'name', 'description', 'icon', 'thread_count as threadCount', 'parentid'
            ])
            ->orderBy('parentid', 'asc')
            ->orderBy('sort')
            ->get()->toArray();

        $categoriesFather = [];
        $categoriesChild = [];

        foreach ($categories as $category) {
            $canCreateThread = $this->userRepo->canCreateThread($this->user, $category['pid']);
            $category['canCreateThread'] = $canCreateThread;
            $category['canEditThread']   = false;

            // 草稿
            if (isset($threadData) && !empty($threadData) && $threadData['is_draft'] == Thread::BOOL_YES) {
                // 只有作者本人可以编辑，且分类对应展示发帖分类
                if ($threadData->user_id == $this->user->id) {
                    $category['canEditThread'] = $canCreateThread;
                } else {
                    $category['canEditThread'] = false;
                }
            }

            // 非草稿
            if (isset($threadData) && !empty($threadData) && $threadData['is_draft'] == Thread::BOOL_NO) {
                // 如果是作者本人，先判断有无 “自我编辑” 权限
                if ($threadData->user_id == $this->user->id && 
                    $this->userRepo->canEditMyThread($this->user,  $category['pid'])) {
                        $category['canEditThread'] = true;
                }else {
                    // 不是本人 或 作者无自我编辑权限，判断有无 “编辑” 权限
                    $category['canEditThread'] = $this->userRepo->canEditOthersThread($this->user,  $category['pid']);
                }
            }

            if ($category['parentid'] !== 0) {
                $categoriesChild[$category['parentid']][] = $category;
            } else {
                $categoriesFather[] = $category;
            }
        }

        // 获取一级分类的二级子类
        foreach ($categoriesFather as $key => $value) {
            if (isset($categoriesChild[$value['pid']])) {
                $categoriesFather[$key]['children'] = $categoriesChild[$value['pid']];
            } else {
                $categoriesFather[$key]['children'] = [];
            }
        }

        $this->outPut(ResponseCode::SUCCESS, '', $categoriesFather);
    }
}
