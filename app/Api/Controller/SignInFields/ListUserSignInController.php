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

namespace App\Api\Controller\SignInFields;

use App\Api\Serializer\UserSignInSerializer;
use App\Models\UserSignInFields;
use Discuz\Api\Controller\AbstractListController;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListUserSignInController extends AbstractListController
{
    public $serializer = UserSignInSerializer::class;
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $params = $request->getQueryParams();
        $userId = Arr::get($params, 'user_id');
        if(empty($userId)){
            throw new \Exception('user_id不能为空');
        }
        return UserSignInFields::instance()->getUserSignInFields($userId);
    }
}
