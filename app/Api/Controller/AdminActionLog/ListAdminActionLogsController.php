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

namespace App\Api\Controller\AdminActionLog;

use App\Api\Serializer\AdminActionLogSerializer;
use App\Models\User;
use App\Models\AdminActionLog;
use App\Repositories\AdminActionLogsRepository;
use Discuz\Api\Controller\AbstractListController;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Exception\InvalidParameterException;

class ListAdminActionLogsController extends AbstractListController
{
    use AssertPermissionTrait;

    /**
     * {@inheritdoc}
     */
    public $serializer = AdminActionLogSerializer::class;

    /**
     * {@inheritdoc}
     */
    public $sortFields = [
        'created_at',
        'updated_at',
    ];

    /**
     * {@inheritdoc}
     */
    public $sort = [
        'created_at' => 'desc',
    ];

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var UserWalletLogsRepository
     */
    protected $adminActionLogs;

    /**
     * @var int
     */
    protected $total;

    /**
     * @var int
     */
    protected $sumChangeAvailableAmount;

    /**
     * @param Dispatcher $bus
     * @param UrlGenerator $url
     * @param AdminActionLogsRepository $adminActionLogs
     */
    public function __construct(Dispatcher $bus, UrlGenerator $url, AdminActionLogsRepository $adminActionLogs)
    {
        $this->bus = $bus;
        $this->url = $url;
        $this->adminActionLogs = $adminActionLogs;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Document $document
     * @return Collection|mixed
     * @throws \Discuz\Auth\Exception\NotAuthenticatedException
     * @throws \Tobscure\JsonApi\Exception\InvalidParameterException
     */
    public function data(ServerRequestInterface $request, Document $document)
    {
        $this->assertAdmin($request->getAttribute('actor'));
        $actor = $request->getAttribute('actor');

        $filter = $this->extractFilter($request);
        $sort = $this->extractSort($request);
        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);

        $adminActionLogs = $this->search($actor, $filter, $sort, $limit, $offset);

        $document->addPaginationLinks(
            $this->url->route('adminactionlog.log.list'),
            $request->getQueryParams(),
            $offset,
            $limit,
            $this->total
        );

        $document->setMeta([
            'total' => $this->total,
            'pageCount' => ceil($this->total / $limit),
            'sumChangeAvailableAmount' => $this->sumChangeAvailableAmount,
        ]);

        return $adminActionLogs;
    }

    /**
     * @param $actor
     * @param $filter
     * @param $sort
     * @param null $limit
     * @param int $offset
     * @return Collection
     */
    public function search($actor, $filter, $sort, $limit = null, $offset = 0)
    {
        $query = $this->adminActionLogs->query()->whereVisibleTo($actor);

        $this->applyFilters($query, $filter, $actor);

        $this->total = $limit > 0 ? $query->count() : null;

        $query->skip($offset)->take($limit);

        foreach ((array)$sort as $field => $order) {
            $query->orderBy(Str::snake($field), $order);
        }

        return $query->get();
    }

    /**
     * @param Builder $query
     * @param array $filter
     * @param User $actor
     */
    private function applyFilters(Builder $query, array $filter, User $actor)
    {
        $log_user = (int)Arr::get($filter, 'user'); //用户
        $log_action_desc = Arr::get($filter, 'action_desc'); //操作描述
        $log_username = Arr::get($filter, 'username'); //操作人
        $log_start_time = Arr::get($filter, 'start_time'); //变动时间范围：开始
        $log_end_time = Arr::get($filter, 'end_time'); //变动时间范围：结束

        if($log_user){
            $query->when($log_user, function ($query) use ($log_user) {
                $query->where('user_id', $log_user);
            });
        }

        if($log_action_desc){
            $query->when($log_action_desc, function ($query) use ($log_action_desc) {
                $query->where('action_desc', 'like', "%$log_action_desc%");
            });
        }
        
        if($log_start_time){
            $query->when($log_start_time, function ($query) use ($log_start_time) {
                $query->where('created_at', '>=', $log_start_time);
            });
        }
        
        if($log_end_time){
            $query->when($log_end_time, function ($query) use ($log_end_time) {
                $query->where('created_at', '<=', $log_end_time);
            });
        }
        
        if($log_username){
            $query->when($log_username, function ($query) use ($log_username) {
                $query->whereIn('admin_action_logs.user_id', User::where('users.username', $log_username)->select('id', 'username')->get());
            });
        }
    }
}
