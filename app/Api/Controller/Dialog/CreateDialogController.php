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

namespace App\Api\Controller\Dialog;

use App\Api\Serializer\DialogSerializer;
use App\Commands\Dialog\CreateDialog;
use Discuz\Api\Controller\AbstractCreateController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Illuminate\Contracts\Validation\Factory;

class CreateDialogController extends AbstractCreateController
{
    public $serializer = DialogSerializer::class;

    protected $validation;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus,Factory $validation)
    {
        $this->validation = $validation;
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $attributes = Arr::get($request->getParsedBody(), 'data.attributes');

        $this->validation->make($attributes, [
            'message_text'  => 'required_without:message_text|max:450',
        ])->validate();

        return $this->bus->dispatch(
            new CreateDialog($actor, $attributes)
        );
    }
}
