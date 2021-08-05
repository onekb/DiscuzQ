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

use App\Models\Category;
use App\Common\ResponseCode;
use App\Library\Json;
use Discuz\Base\DzqController;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;

class BatchDeleteCategoriesController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有删除分类权限');
        }
        return true;
    }

    public function main()
    {
        $idString  = $this->inPut('id');
        if(empty($idString)){
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '未获取到必要参数', '');
        }
        $ids = explode(',', $idString);

        // 批量添加的限制
        if(count($ids) > 100){
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '批量添加超过限制', '');
        }

        foreach ($ids as $id){
            if($id < 1){
                return $this->outPut(ResponseCode::INVALID_PARAMETER, '', '');
            }
        }
        $categoryDatas = Category::query()->whereIn('id', $ids)->get();
        $categoryModel = new Category();
        $categoryDatas->map(function ($category) use ($categoryModel) {
            if($categoryModel->hasThreads($category->id,"normal")){
                return $this->outPut(ResponseCode::INTERNAL_ERROR, '无法删除存在主题的分类', '分类名：' . $category->name);
            }else{
                $category->delete();
            }
        });

        return $this->outPut(ResponseCode::SUCCESS, '', '');
    }
}
