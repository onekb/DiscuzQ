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

namespace App\Api\Controller\PermissionV3;

use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Events\Group\PermissionUpdated;
use App\Models\Group;
use App\Models\Permission;
use App\Models\AdminActionLog;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Events\Dispatcher;

class UpdateGroupPermissionController extends DzqController
{
    public function suffixClearCache($user)
    {
        DzqCache::delKey(CacheKey::GROUP_PERMISSIONS);
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    protected $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function main()
    {
        $actor = $this->user;
        $groupId = (int)$this->inPut('groupId');
        $permissions = $this->inPut('permissions');

        /** @var Group $group */
        $group = Group::query()->findOrFail($groupId);

        $oldPermissions = Permission::query()->where('group_id', $group->id)->pluck('permission');

        // 合并默认权限，去空，去重
        $newPermissions = collect($permissions)
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
            '更改用户角色【' . $group->name . '】操作权限'
        );

        return $this->outPut(ResponseCode::SUCCESS);
    }
}
