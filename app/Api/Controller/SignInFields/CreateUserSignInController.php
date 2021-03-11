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
use App\Commands\SignInFields\CreateUserSignIn;
use App\Models\User;
use Discuz\Api\Controller\AbstractCreateController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Contracts\Validation\Factory;
use Discuz\Auth\AssertPermissionTrait;
use App\Models\Setting;
use Discuz\Auth\Exception\PermissionDeniedException;

class CreateUserSignInController extends AbstractCreateController
{
    use AssertPermissionTrait;

    public $serializer = UserSignInSerializer::class;

    private $bus;

    protected $validation;

    protected $setting;
    public function __construct(Dispatcher $bus, Factory $validation, Setting $setting)
    {
        $this->validation = $validation;
        $this->setting = $setting;
        $this->bus = $bus;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $ip = ip($request->getServerParams());
        $port = Arr::get($request->getServerParams(), 'REMOTE_PORT', 0);
        $data = $request->getParsedBody()->get('data');

        $this->assertBatchData($data);

        foreach ($data as $k=>$v) {
            $this->validation->make($data[$k]['attributes'], [
                'name'  => 'sometimes|max:20',
                'fields_ext'  => 'sometimes|max:20000',
                'fields_desc'  => 'sometimes|max:20000',
            ])->validate();
        }

        if ($actor->status != User::STATUS_NEED_FIELDS) {
            $isOpen = $this->setting->where('key', 'open_ext_fields')->where('value', 1)->count();
            if ($isOpen == 0) {
                throw new PermissionDeniedException;
            }
        }

        return $this->bus->dispatch(new CreateUserSignIn($actor,$data,$ip,$port));
    }
}
