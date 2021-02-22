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

namespace App\Api\Controller\Threads;

use Carbon\Carbon;
use App\Models\Category;
use App\Models\Order;
use App\Models\Post;
use App\Models\PostUser;
use App\Models\Thread;
use App\Models\User;
use App\Models\ThreadRedPacket;
use App\Models\ThreadReward;
use App\Api\Serializer\ThreadSerializer;
use App\Repositories\ThreadRepository;
use App\Repositories\TopicRepository;
use Discuz\Api\Controller\AbstractListController;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Common\Utils;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Exception\InvalidParameterException;

class ListRecommendedThreadsController extends AbstractListController
{
    use AssertPermissionTrait;

    /**
     * {@inheritdoc}
     */
    public $serializer = ThreadSerializer::class;

    /**
     * @param ServerRequestInterface $request
     * @param Document $document
     * @return Collection|mixed
     * @throws InvalidParameterException
     * @throws PermissionDeniedException
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');

        $typeId = [Thread::TYPE_OF_TEXT, Thread::TYPE_OF_LONG];
        $red_threads = Thread::query()
            ->whereVisibleTo($actor)
            ->join('thread_red_packets', 'threads.id', '=', 'thread_red_packets.thread_id')
            ->where(['threads.is_approved' => 1, 'threads.is_draft' => 0, 'threads.is_red_packet' => 1, 'threads.is_display' => 1, 'thread_red_packets.status' => 1])
            ->where('thread_red_packets.remain_number', '>', 0)
            ->whereNull('threads.deleted_at')
            ->whereIn('threads.type', $typeId)
            ->pluck('threads.id')
            ->toArray();

        $reward_threads = Thread::query()
            ->whereVisibleTo($actor)
            ->join('thread_rewards', 'threads.id', '=', 'thread_rewards.thread_id')
            ->where(['threads.is_approved' => 1, 'threads.is_draft' => 0, 'threads.is_display' => 1, 'thread_rewards.type' => 0])
            ->where('thread_rewards.remain_money', '>', 0)
            ->where('thread_rewards.expired_at', '>', Carbon::now())
            ->whereNull('threads.deleted_at')
            ->where('threads.type', Thread::TYPE_OF_QUESTION)
            ->pluck('threads.id')
            ->toArray();

        $recommend_thread_ids = array_merge($red_threads, $reward_threads);
        if(empty($recommend_thread_ids)){
            return [];
        }
        $recommend_thread_ids = array_flip($recommend_thread_ids);
        $recommend_thread_ids = array_keys($recommend_thread_ids);
        $recommend_thread_ids = array_reverse($recommend_thread_ids);
        shuffle($recommend_thread_ids);

        if(count($recommend_thread_ids) > 5){
            $recommend_thread_ids = array_slice($recommend_thread_ids, 1, 5);
        }

        $recommendThreadData = Thread::query()->whereIn('id', $recommend_thread_ids)->get();

        return $recommendThreadData;
    }
}