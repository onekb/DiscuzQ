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

use App\Common\CacheKey;
use App\Events\Group\PaidGroup;
use App\Models\Group;
use App\Models\GroupPaidUser;
use App\Models\User;
use Discuz\Cache\CacheManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Carbon;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CheckPaidUserGroupMiddleware implements MiddlewareInterface
{
    const CHECK_INTERVAL = 120;

    public $events;
    protected $cache;

    public function __construct(Dispatcher $events, CacheManager $cache)
    {
        $this->events = $events;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var User $actor */
        $actor = $request->getAttribute('actor');

        //if (app()->config('middleware_cache')) {
        // 通过加锁，避免每次请求都判断
        $ttl = static::CHECK_INTERVAL;
        if ($this->cache->add(CacheKey::CHECK_PAID_GROUP.$actor->id, 'lock', mt_rand($ttl, $ttl + 10))) {
            $this->check($actor);
        }
        /*} else {
            $this->check($actor);
        }*/

        return $handler->handle($request);
    }

    protected function check(User $actor)
    {
        if ($actor->groups->count() && !$actor->isGuest()) {
            //检查到期付费用户组
            $groups = $actor->groups()->where('is_paid', Group::IS_PAID)->get();

            if ($groups->count()) {
                $now = Carbon::now();
                foreach ($groups as $group => $group_item) {
                    if (empty($group_item->pivot->expiration_time)) {
                        //免费组变为收费组
                        $this->events->dispatch(
                            new PaidGroup($group_item->id, $actor)
                        );
                    } elseif ($group_item->pivot->expiration_time < $now) {
                        GroupPaidUser::query()
                            ->where('group_id', $group_item->pivot->group_id)
                            ->where('user_id', $group_item->pivot->user_id)
                            ->update(['deleted_at' => $now, 'delete_type' => GroupPaidUser::DELETE_TYPE_EXPIRE]);
                        $actor->groups()->detach($group_item);
                    }
                }
            }

            // 如果付费用户组到期后用户没有其他用户组，将其添加到默认用户组
            if (! $actor->groups()->count()) {
                $actor->resetGroup();
            }
        }
    }
}
