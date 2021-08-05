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

use App\Api\Controller\UsersV3\AuthBaseController;
use App\Api\Serializer\UserSignInSerializer;
use App\Commands\SignInFields\CreateUserSignIn;
use App\Common\ResponseCode;
use App\Models\User;
use App\Models\UserSignInFields;
use App\Repositories\UserRepository;
use Discuz\Api\Controller\AbstractCreateController;
use Discuz\Base\DzqController;
use Discuz\Base\DzqLog;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Contracts\Validation\Factory;
use Discuz\Auth\AssertPermissionTrait;
use App\Models\Setting;
use Discuz\Auth\Exception\PermissionDeniedException;

class CreateUserSignInController extends AuthBaseController
{
    private $bus;
    protected $validation;
    protected $setting;
    public function __construct(Dispatcher $bus, Factory $validation, Setting $setting)
    {
        $this->validation = $validation;
        $this->setting = $setting;
        $this->bus = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        try {
            $actor  = $this->user;
            $ip     = ip($this->request->getServerParams());
            $port   = Arr::get($this->request->getServerParams(), 'REMOTE_PORT', 0);
            $data   = $this->inPut('data');

            foreach ($data as $k=>$v) {

                $this->validation->make($data[$k], [
                    'name'         => 'sometimes|max:20',
                    'fieldsExt'    => 'sometimes|max:20000',
                    'fieldsDesc'   => 'sometimes|max:20000',
                ])->validate();
            }

            if ($actor->status != User::STATUS_NEED_FIELDS) {
                $isOpen = $this->setting->where('key', 'open_ext_fields')->where('value', 1)->count();
                if ($isOpen == 0) {
                    throw new PermissionDeniedException;
                }
            }

            $result = $this->userSaveUserSignInFields($actor->id,$data);
            $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($result));
        } catch (\Exception $e) {
            DzqLog::error('create_user_sign_in_api_error', $data, $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '创建扩展字段接口异常');
        }
    }

    /**
     *用户新建或编辑扩展字段内容
     * @param $userId
     * @param $attributes
     * @return array
     */
    public function userSaveUserSignInFields($userId, $attributes)
    {
        if (empty($userId)) throw new PermissionDeniedException('用户id错误');
        $settings = app(SettingsRepository::class);
        $register_validate = $settings->get('register_validate');
        if ($register_validate) {
            if (User::isStatusMod($userId)) {
                throw new PermissionDeniedException('register_validate');
            }
        }
        $data = [];
        foreach ($attributes as $attribute) {
            if (!empty($attribute['id'])) {//更新
                $userSignIn = UserSignInFields::query()->where('id', $attribute['id'])
                    ->where('status', '!=', UserSignInFields::STATUS_DELETE)
                    ->where('user_id', $userId)
                    ->first();
                if (empty($userSignIn)) {
                    continue;
                }
                if ($userSignIn['status'] == UserSignInFields::STATUS_REJECT) {
                    $userSignIn->setAttribute('status', UserSignInFields::STATUS_DELETE);
                    $userSignIn->save();
                    $userSignIn = new UserSignInFields();
                }
            } else {//新建
                $userSignIn = new UserSignInFields();
            }
            $rawData = [
                'user_id'       => $userId,
                'name'          => !empty($attribute['name']) ? $attribute['name'] : '',
                'type'          => !empty($attribute['type']) ? $attribute['type'] : 0,
                'fields_ext'    => !empty($attribute['fieldsExt']) ? $attribute['fieldsExt'] : '',
                'fields_desc'   => !empty($attribute['fieldsDesc']) ? $attribute['fieldsDesc'] : '',
                'sort'          => !empty($attribute['sort']) ? $attribute['sort'] : 0,
                'status'        => UserSignInFields::STATUS_AUDIT,
                'required'      => !empty($attribute['required']) ? $attribute['required'] : 0
            ];
            $userSignIn->setRawAttributes($rawData);
            $userSignIn->save();
            $data[] = $userSignIn;
        }
        //修改user的status为2，待审核状态
        if ($register_validate) {
            User::setUserStatusMod($userId);
        } else {
            User::setUserStatusNormal($userId);
        }
        return $data;
    }
}
