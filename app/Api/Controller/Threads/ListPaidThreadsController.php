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

class ListPaidThreadsController extends AbstractListController
{
    use AssertPermissionTrait;

    /**
     * {@inheritdoc}
     */
    public $serializer = ThreadSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $include = [
        'firstPost',
        'threadVideo',
        'threadAudio',
        'lastPostedUser',
        'category',
    ];

    public $mustInclude = [
        'user',
        'favoriteState',
        'firstPost.likeState',
        'question',
        'onlookerState',
    ];

    /**
     * @var ThreadRepository
     */
    protected $threads;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var int|null
     */
    protected $threadCount;

    /**
     * @var string
     */
    protected $tablePrefix;

    private $threadCache;

    /**
     * @param ThreadRepository $threads
     * @param UrlGenerator $url
     */
    public function __construct(ThreadRepository $threads, UrlGenerator $url)
    {
        $this->threads = $threads;
        $this->url = $url;
        $this->tablePrefix = config('database.connections.mysql.prefix');
        $this->cache = app('cache');
    }

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
        $filter = $this->extractFilter($request);
        $params['isMobile'] = Utils::isMobile();
        $sort = $this->extractSort($request);

        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);
        $include = $this->extractInclude($request);

        $orderType = [Order::ORDER_TYPE_THREAD, ORDER::ORDER_TYPE_ATTACHMENT];
        $order_thread_ids = Order::query()
                        ->where(['user_id' => $actor->id, 'status' => 1])
                        ->whereIn('type', $orderType)
                        ->pluck('thread_id');
        $thread_ids = $order_thread_ids->toArray();

        $query = $this->threads->query()->select('threads.*');
        $query->whereIn('id', $thread_ids);
        $query->orderBy('created_at', 'desc');
        $threads = $query->get();

        $this->threadCount = count($threads);

        $this->addDocument($document, $params, $this->threadCount, $offset, $limit);

        $threads->loadMissing($include);
        
        if ($relations = array_intersect($include, ['firstPost'])) {
            $threads->map(function ($thread) use ($relations) {
                foreach ($relations as $relation) {
                    if ($thread->$relation) {
                        $thread->$relation->thread = $thread;
                    }
                }
            });
        }

        return $threads;
    }

    private function addDocument($document, $params, $count, $offset, $limit)
    {
        $document->addPaginationLinks(
            $this->url->route('threads.paid'),
            $params,
            $offset,
            $limit,
            $count
        );
        $document->setMeta([
            'threadCount' => $count,
            'pageCount' => ceil($count / $limit),
        ]);
    }
}