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

namespace App\Api\Controller\Trade\Notify;

use Discuz\Http\DiscuzResponseFactory;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Commands\Trade\Notify\WechatNotify;
use Discuz\Api\Controller\AbstractResourceController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class WechatNotifyController extends AbstractResourceController
{
    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $document = new Document();
        $data     = $this->data($request, $document);
        $result = DiscuzResponseFactory::XmlResponse($data);
        if ($result) {
            if (is_object($result)) {
                $result = json_encode($result);
            }
            if (is_array($result)) {
                $result = implode(',', $result);
            }
            app('log')->info('apiv1-result-handle记录：' . $result);
        }

        return DiscuzResponseFactory::XmlResponse($data);
    }

    public function data(ServerRequestInterface $request, Document $document)
    {
        $result = $this->bus->dispatch(
            new WechatNotify($request->getParsedBody())
        );
        if ($result) {
            if (is_object($result)) {
                $result = json_encode($result);
            }
            if (is_array($result)) {
                $result = implode(',', $result);
            }

            app('log')->info('apiv1-result-data记录：' . $result);
        }

        return $this->bus->dispatch(
            new WechatNotify($request->getParsedBody())
        );
    }
}
