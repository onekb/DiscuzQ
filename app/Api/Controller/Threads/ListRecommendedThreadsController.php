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

        $typeId = array('0','1');
        $red_threads = Thread::query()
            ->whereVisibleTo($actor)
            ->where(['is_approved' => 1, 'is_draft' => 0, 'is_red_packet' => 1])
            ->whereNull('deleted_at')
            ->wherein('type', $typeId)
            ->get();
        $reward_threads = Thread::query()
            ->whereVisibleTo($actor)
            ->where(['is_approved' => 1, 'is_draft' => 0])
            ->whereNull('deleted_at')
            ->where('type', 5)
            ->get();
        
        $red_threads = json_decode($red_threads, true);
        $reward_threads = json_decode($reward_threads, true);
        $threads = $red_threads + $reward_threads;
        $threads = array_merge($threads);
        $thread_ids = array_column($threads, 'id');

        // get thread's content
        $thread_content = Post::query()->where('is_first', 1)->wherein('thread_id', $thread_ids)->get();
        $thread_content = json_decode($thread_content, true);
        $thread_content = array_column($thread_content, null, 'thread_id');

        // get thread's recommendability
        $thread_red_packet = ThreadRedPacket::query()->where('remain_number', '>', 0)->where('status', 1)->wherein('thread_id', $thread_ids)->get();
        $thread_red_packet = json_decode($thread_red_packet, true);
        $thread_red_packet = array_column($thread_red_packet, null, 'thread_id');

        $thread_question = ThreadReward::query()->where('type', 0)->where('remain_money', '>', 0)->where('expired_at', '>', Carbon::now())->wherein('thread_id', $thread_ids)->get();
        $thread_question = json_decode($thread_question, true);
        $thread_question = array_column($thread_question, null, 'thread_id');

        // reorganize the array
        if(isset($threads) && !empty($threads)){
            foreach ($threads as $key => $value) {
                if(!isset($thread_red_packet[$value['id']]) && !isset($thread_question[$value['id']])){
                    unset($threads[$key]);
                }
            }
        }

        $threads = array_merge($threads);
        $recommend_thread_ids = array_column($threads, 'id');
        shuffle($recommend_thread_ids);

        if(count($recommend_thread_ids) > 5){
            $recommend_thread_ids = array_slice($recommend_thread_ids, 1, 5);
        }

        return Thread::query()->wherein('id', $recommend_thread_ids)->get();
    }
}