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

namespace App\Api\Controller\Threads\Notify;

use App\Commands\Thread\Notify\ThreadVideoNotify;
use Discuz\Http\DiscuzResponseFactory;
use Illuminate\Contracts\Bus\Dispatcher;
use Discuz\Api\Controller\AbstractResourceController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Support\Arr;

class ThreadVideoNotifyController extends AbstractResourceController
{
    /**
     * @var Dispatcher
//     */
    protected $bus;

    protected $settings;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus,SettingsRepository $settings)
    {
        $this->bus = $bus;
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $document = new Document();

        $dbtoken = $this->settings->get('qcloud_vod_token', 'qcloud');
        $inputtoken = Arr::get($request->getQueryParams(), 'qvodtoken');
        if (empty($dbtoken) || (!empty($inputtoken) && strcmp($dbtoken, $inputtoken) === 0))
        {
            $data     = $this->data($request, $document);
            return DiscuzResponseFactory::XmlResponse($data);
        }
        else
        {
            return DiscuzResponseFactory::XmlResponse("fobidden");
        }
    }

    public function data(ServerRequestInterface $request, Document $document)
    {
        return $this->bus->dispatch(
            new ThreadVideoNotify($request->getParsedBody()->toArray())
        );
    }
}
