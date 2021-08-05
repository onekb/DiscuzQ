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

class BatchUpdateCategoriesController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有更新分类的权限');
        }
        return true;
    }

    public function main()
    {
        $data = $this->inPut('data');
        $ip   = ip($this->request->getServerParams());

        // 批量添加的限制
        if(count($data) > 100){
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '批量添加超过限制', '');
        }

        $resultData = [];
        $validate = app('validator');
        foreach ($data as $key => $value) {
            try{
                $validate->validate($value, [
                    'id'            => 'required|int|min:1',
                    'name'          => 'required|min:1|max:20',
                    'sort'          => 'required|int',
                    'description'   => 'sometimes|max:200'
                ]);

                 $category = Category::query()->findOrFail($value['id']);
                if(isset($value['name'])){
                    $category->name = $value['name'];
                }

                if(isset($value['description'])){
                    $category->description = $value['description'];
                }

                if(isset($value['sort'])){
                    $category->sort = $value['sort'];
                }
                $category->ip = $ip;
                $category->save();
                $resultData[] = $category;
            } catch (\Exception $e) {
                app('log')->info('requestId：' . $this->requestId . '-' . '修改内容分类 "' . $value['name'] . '" 出错： ' . $e->getMessage());
                return $this->outPut(ResponseCode::INTERNAL_ERROR, '修改出错', [$e->getMessage(), $value]);
            }
        }

        return $this->outPut(ResponseCode::SUCCESS, '', '');
    }
}
