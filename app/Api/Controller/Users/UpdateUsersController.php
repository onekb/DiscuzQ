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

namespace App\Api\Controller\Users;

use App\Api\Serializer\InfoSerializer;
use App\Commands\Users\UpdateUser;
use App\Models\User;
use Discuz\Api\Controller\AbstractListController;
use Discuz\Auth\AssertPermissionTrait;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateUsersController extends AbstractListController
{
    use AssertPermissionTrait;

    public $serializer = InfoSerializer::class;

    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $multipleData = Arr::get($request->getParsedBody(), 'data', []);
        $this->assertBatchData($multipleData, false);

        $list = collect();
        foreach ($multipleData as $data) {
            $id = Arr::get($data, 'attributes.id');

            try {
                $item = $this->bus->dispatch(
                    new UpdateUser($id, ['data' => $data], $actor)
                );

                $item->succeed = true;
            } catch (\Exception $e) {
                $item = new User;

                $item->id = $id;
                $item->succeed = false;
                $item->error = $e->getMessage();
            }

            $list->push($item);
        }

        return $list;
    }
}
