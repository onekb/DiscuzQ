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

namespace App\Api\Middleware;

use App\Commands\Order\RefundErrorThreadOrder;
use Discuz\Cache\CacheManager;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Database\ConnectionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class CreateThreadOrderRefundMiddleware implements MiddlewareInterface
{
    protected $log;

    protected $cache;

    public function __construct(LoggerInterface $log, CacheManager $cache)
    {
        $this->log = $log;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->cache->add('create_thread_order_refund', 'lock', 60)) {
            try {
                /** @var RefundErrorThreadOrder $command */
                $command = app(RefundErrorThreadOrder::class);
                app()->call([$command, 'handle']);
            } catch (Exception $e) {
                $this->log->info('中间件处理发帖订单失败：' . $e->getMessage());
            }
        }

        return $handler->handle($request);
    }
}
