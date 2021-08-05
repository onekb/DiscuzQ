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
use App\Common\ResponseCode;
use App\Models\UserSignInFields;
use App\Repositories\UserRepository;
use Discuz\Api\Controller\AbstractListController;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Discuz\Base\DzqLog;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListUserSignInController extends AuthBaseController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            app('log')->info(
                '[infoParam:]'
                . '[requestId:]' . $this->requestId
                . ';[requestIP:]' . ip($this->request->getServerParams())
                . ';[requestTarget:]' . $this->request->getRequestTarget()
                . ';[remake:]' . '没有访问扩展字段的权限'
                . ';[user:]' . json_encode($actor)
            );
            throw new PermissionDeniedException('没有访问扩展字段的权限');
        }
        return true;
    }

    public function main()
    {
        try {
            $userId = $this->user->id;
            if(empty($userId)){
                $this->outPut(ResponseCode::USERID_NOT_ALLOW_NULL);
            }

            $result = UserSignInFields::instance()->getUserSignInFields($userId);

            $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($result));
        } catch (\Exception $e) {
            DzqLog::error('list_user_sign_in_api_error', [], $e->getMessage());
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '扩展字段查询接口异常');
        }
    }
}
