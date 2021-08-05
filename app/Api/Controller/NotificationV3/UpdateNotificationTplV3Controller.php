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

namespace App\Api\Controller\NotificationV3;

use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use App\Models\NotificationTpl;
use Discuz\Auth\Exception\PermissionDeniedException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Discuz\Base\DzqController;

class UpdateNotificationTplV3Controller extends DzqController
{
    use NotificationTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if (!$actor->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    public function main()
    {
        $data = $this->inPut('data');

        $tpl = NotificationTpl::query()->whereIn('id', array_column($data,'id'))->get()->keyBy('id');

        collect($data)->map(function ($attributes) use ($tpl) {
            if ($notificationTpl = $tpl->get(Arr::get($attributes, 'id'))) {
                $this->updateTpl($notificationTpl, $attributes);
            }
        });

        $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($this->getDefaultAttributes($tpl)));
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
                $this->dzqValidate($attributes, [
                    'title' => 'filled',
                ]);

                if (Arr::has($attributes, 'title')) {
                    $notificationTpl->title = Arr::get($attributes, 'title');
                }
                if (Arr::has($attributes, 'content')) {
                    $notificationTpl->content = Arr::get($attributes, 'content');
                }
                break;
            default:
                if ($notificationTpl->status == 1) {
                    $this->dzqValidate($attributes, [
                        'templateId' => 'filled',
                    ]);
                }

                if (Arr::has($attributes, 'templateId')) {
                    $templateId = Arr::get($attributes, 'templateId');
                    if ($notificationTpl->template_id != $templateId) {
                        $notificationTpl->template_id = Arr::get($attributes, 'templateId');

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

        if (isset($attributes['firstData'])) {
            $notificationTpl->first_data = Arr::get($attributes, 'firstData');
        }

        if (isset($attributes['keywordsData'])) {
            $keywords = array_map(function ($keyword) {
                return str_replace(',', '，', $keyword);
            }, (array) Arr::get($attributes, 'keywordsData', []));

            $notificationTpl->keywords_data = implode(',', $keywords);
        }

        if (isset($attributes['remarkData'])) {
            $notificationTpl->remark_data = Arr::get($attributes, 'remarkData');
        }

        if (isset($attributes['color'])) {
            $notificationTpl->color = Arr::get($attributes, 'color');
        }

        if (isset($attributes['redirectType'])) {
            $notificationTpl->redirect_type = (int) Arr::get($attributes, 'redirectType');
        }

        if (isset($attributes['redirectUrl'])) {
            $notificationTpl->redirect_url = Arr::get($attributes, 'redirectUrl');
        }

        if (isset($attributes['pagePath'])) {
            $notificationTpl->page_path = Arr::get($attributes, 'pagePath');
        }

        $notificationTpl->save();

        return $notificationTpl;
    }
}
