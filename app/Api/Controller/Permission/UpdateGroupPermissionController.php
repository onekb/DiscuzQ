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

namespace App\Api\Controller\Permission;

use App\Api\Serializer\GroupPermissionSerializer;
use App\Events\Group\PermissionUpdated;
use App\Models\Group;
use App\Models\Permission;
use App\Models\AdminActionLog;
use App\Models\Setting;
use App\Settings\SettingsRepository;
use Discuz\Api\Controller\AbstractListController;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Http\DiscuzResponseFactory;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UpdateGroupPermissionController extends AbstractListController
{
    use AssertPermissionTrait;

    /**
     * {@inheritdoc}
     */
    public $serializer = GroupPermissionSerializer::class;

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var SettingsRepository
     */
    public $settings;

    /**
     * @param Dispatcher $events
     */
    public function __construct(Dispatcher $events, SettingsRepository $settings)
    {
        $this->events = $events;
        $this->settings = $settings;
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

        $this->assertCan($actor, 'group.edit');

        $attributes = Arr::get($request->getParsedBody(), 'data.attributes');

        /** @var Group $group */
        $group = Group::query()->findOrFail((int) Arr::get($attributes, 'groupId'));

        // 查看请求的权限中是否有与全局权限相交的，如果有需要判断
        $request_permissions = $attributes['permissions'] ?? [];
        $global_permissions = [];
        $setting_global_permission = Setting::$global_permission;
        foreach ($setting_global_permission as $val){
            $global_permissions = array_merge($global_permissions, $val);
        }
        $judge_permissions = array_intersect($request_permissions, $global_permissions);
        $settings = app(SettingsRepository::class);
        if(!empty($judge_permissions)){
            foreach ($setting_global_permission as $key => $val){
                if(!empty(array_intersect($val, $judge_permissions))){          //如果在对应的全局中，则判断这个全局功能权限是否开启
                    if($settings->get($key, 'default') == 0){
                        throw new PermissionDeniedException($key. 'permission_denied');
                    }
                }
            }
        }
        $oldPermissions = Permission::query()->where('group_id', $group->id)->pluck('permission');

        // 合并默认权限，去空，去重
        $newPermissions = collect(Arr::get($attributes, 'permissions'))
            ->merge(Permission::DEFAULT_PERMISSION)
            ->filter()
            ->unique();
        Permission::query()->where('group_id', $group->id)->delete();
        Permission::query()->insert($newPermissions->map(function ($item) use ($group) {
            return ['group_id' => $group->id, 'permission' => $item];
        })->toArray());

        $this->events->dispatch(
            new PermissionUpdated($group, $oldPermissions, $newPermissions, $actor)
        );

        AdminActionLog::createAdminActionLog(
            $actor->id,
            '更改用户角色【'. $group->name .'】操作权限'
        );

        return DiscuzResponseFactory::EmptyResponse();
    }
}
