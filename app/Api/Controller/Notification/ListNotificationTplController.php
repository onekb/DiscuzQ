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

namespace App\Api\Controller\Notification;

use App\Api\Serializer\ArraySerializer;
use App\Models\NotificationTpl;
use Discuz\Api\Controller\AbstractListController;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Http\UrlGenerator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListNotificationTplController extends AbstractListController
{
    use AssertPermissionTrait;

    /**
     * @var string
     */
    public $serializer = ArraySerializer::class;

    /**
     * @var UrlGenerator
     */
    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Document $document
     * @return NotificationTpl[]|\Illuminate\Database\Eloquent\Collection|mixed
     * @throws \Discuz\Auth\Exception\PermissionDeniedException
     * @throws \Tobscure\JsonApi\Exception\InvalidParameterException
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $this->assertAdmin($request->getAttribute('actor'));

        $limit = $this->extractLimit($request);
        $offset = $this->extractOffset($request);

        $tpl = NotificationTpl::all(['id', 'status', 'type', 'type_name', 'is_error', 'error_msg'])->groupBy('type_name');

        $total = $tpl->count();

        $data = $tpl->skip($offset)->take($limit);

        $document->addPaginationLinks(
            $this->url->route('notification.tpl.list'),
            $request->getQueryParams(),
            $offset,
            $limit,
            $total
        );

        $document->setMeta([
            'total' => $total,
            'pageCount' => ceil($total / $limit),
        ]);

        return $this->build($data);
    }

    /**
     * @param Collection $data
     * @return Collection
     */
    private function build(Collection $data)
    {
        return $data->map(function (Collection $item, $index) {
            // Splicing typeName
            $typeStatus = [];
            $errorArr = [];
            $item->each(function ($value) use (&$typeStatus, &$errorArr) {
                /** @var NotificationTpl $value */
                if ($value->status) {
                    $build = [
                        'status' => $value->status,
                        'type' => NotificationTpl::enumTypeName($value->type),
                        'is_error' => $value->is_error,
                        'error_msg' => $value->error_msg,
                    ];
                    array_push($typeStatus, $build);
                }
            });

            return [
                'name' => $index,
                'type_status' => $typeStatus,
            ];
        })->values();
    }
}

