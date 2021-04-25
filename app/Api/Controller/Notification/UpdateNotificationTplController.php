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

use App\Api\Serializer\NotificationTplSerializer;
use App\Common\CacheKey;
use App\Models\NotificationTpl;
use Discuz\Api\Controller\AbstractListController;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Tobscure\JsonApi\Document;

class UpdateNotificationTplController extends AbstractListController
{
    use AssertPermissionTrait;

    /**
     * {@inheritdoc}
     */
    public $serializer = NotificationTplSerializer::class;

    /**
     * @var Factory
     */
    protected $validation;
    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    protected $cache;

    /**
     * @param Factory $validation
     */
    public function __construct(Factory $validation)
    {
        $this->validation = $validation;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Document $document
     * @return mixed
     * @throws PermissionDeniedException
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $this->assertAdmin($actor);

        $data = Arr::pluck($request->getParsedBody()->get('data', []), 'attributes');

        $tpl = NotificationTpl::query()->whereIn('id', Arr::pluck($data, 'id'))->get()->keyBy('id');

        collect($data)->map(function ($attributes) use ($tpl) {
            if ($notificationTpl = $tpl->get(Arr::get($attributes, 'id'))) {
                $this->updateTpl($notificationTpl, $attributes);
            }
        });

        return $tpl;
    }

    /**
     * @param NotificationTpl $notificationTpl
     * @param array $attributes
     * @return NotificationTpl
     * @throws ValidationException
     */
    protected function updateTpl(NotificationTpl $notificationTpl, array $attributes)
    {
        switch ($notificationTpl->type) {
            case 0:
                $this->validation->make($attributes, [
                    'title' => 'filled',
                ])->validate();

                if (Arr::has($attributes, 'title')) {
                    $notificationTpl->title = Arr::get($attributes, 'title');
                }
                if (Arr::has($attributes, 'content')) {
                    $notificationTpl->content = Arr::get($attributes, 'content');
                }
                break;
            default:
                if ($notificationTpl->status == 1) {
                    $this->validation->make($attributes, [
                        'template_id' => 'filled',
                    ])->validate();
                }

                if (Arr::has($attributes, 'template_id')) {
                    $templateId = Arr::get($attributes, 'template_id');
                    if ($notificationTpl->template_id != $templateId) {
                        $notificationTpl->template_id = Arr::get($attributes, 'template_id');

                        // 判断是否修改了小程序模板，清除小程序查询模板的缓存
                        if ($notificationTpl->type == NotificationTpl::MINI_PROGRAM_NOTICE) {
                            app('cache')->forget(CacheKey::NOTICE_MINI_PROGRAM_TEMPLATES);
                        }
                    }
                }
                break;
        }

        if (isset($attributes['status'])) {
            $status = Arr::get($attributes, 'status');
            if ($status == 1 && $notificationTpl->type == 1 && empty($notificationTpl->template_id)) {
                // 验证是否设置模板ID
                throw new RuntimeException('notification_is_missing_template_config');
            }

            $notificationTpl->status = $status;
        }

        if (isset($attributes['first_data'])) {
            $notificationTpl->first_data = Arr::get($attributes, 'first_data');
        }

        if (isset($attributes['keywords_data'])) {
            $keywords = array_map(function ($keyword) {
                return str_replace(',', '，', $keyword);
            }, (array) Arr::get($attributes, 'keywords_data', []));

            $notificationTpl->keywords_data = implode(',', $keywords);
        }

        if (isset($attributes['remark_data'])) {
            $notificationTpl->remark_data = Arr::get($attributes, 'remark_data');
        }

        if (isset($attributes['color'])) {
            $notificationTpl->color = Arr::get($attributes, 'color');
        }

        if (isset($attributes['redirect_type'])) {
            $notificationTpl->redirect_type = (int) Arr::get($attributes, 'redirect_type');
        }

        if (isset($attributes['redirect_url'])) {
            $notificationTpl->redirect_url = Arr::get($attributes, 'redirect_url');
        }

        if (isset($attributes['page_path'])) {
            $notificationTpl->page_path = Arr::get($attributes, 'page_path');
        }

        $notificationTpl->save();

        return $notificationTpl;
    }
}
