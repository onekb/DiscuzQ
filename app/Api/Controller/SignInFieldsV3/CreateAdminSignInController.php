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

namespace App\Api\Controller\SignInFieldsV3;

use App\Common\ResponseCode;
use App\Models\AdminSignInFields;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Support\Arr;

class CreateAdminSignInController extends DzqController
{

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    //兼容老版本斜杠入参
    public function enumerate(){
        return [
            'name' => 'name',
            'type' => 'type',
            'fieldsExt' => 'fields_ext',
            'fieldsDesc' => 'fields_desc',
            'sort'=>'sort',
            'status'=> 'status',
            'required' => 'required'
        ];
    }

    public function main()
    {
        $dataArr = $this->request->getParsedBody()->get('data');

        $retureArr = [];
        foreach ($dataArr as $attribute) {
            if (!empty($attribute['id'])) {
                $adminSignIn = AdminSignInFields::query()->where('id', $attribute['id'])->first();
                if (empty($adminSignIn)) {
                    continue;
                }
            }else{
                $adminSignIn = new AdminSignInFields();
            }
            foreach ($attribute as $key => $value) {
                in_array($key, array_keys($this->enumerate())) && $adminSignIn[$this->enumerate()[$key]] = $value;
            }
            $adminSignIn->save() && $retureArr[] = $adminSignIn;
        }

        $this->outPut(ResponseCode::SUCCESS,'',$this->camelData($retureArr));
    }

}
