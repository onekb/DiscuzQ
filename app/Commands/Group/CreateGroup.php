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

namespace App\Commands\Group;

use App\Events\Group\Created;
use App\Events\Group\Saving;
use App\Models\Group;
use App\Models\User;
use App\Models\AdminActionLog;
use App\Validators\GroupValidator;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Foundation\EventsDispatchTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class CreateGroup
{
    use AssertPermissionTrait;
    use EventsDispatchTrait;

    /**
     * The user performing the action.
     *
     * @var User
     */
    protected $actor;

    /**
     * The attributes of the new group.
     *
     * @var array
     */
    protected $data;

    protected $validator;

    /**
     * @param User $actor The user performing the action.
     * @param array $data The attributes of the new group.
     */
    public function __construct(User $actor, array $data)
    {
        $this->actor = $actor;
        $this->data = $data;
    }

    public function handle(Dispatcher $events, GroupValidator $validator)
    {
        $this->events = $events;
        $this->validator = $validator;

        return call_user_func([$this, '__invoke']);
    }

    /**
     * @return Group
     * @throws PermissionDeniedException
     */
    public function __invoke()
    {
        $this->assertCan($this->actor, 'create');

        $group = new Group();

        $group->name = $this->data['name'];
        $group->type = $this->data['type'];
        $group->color = $this->data['color'];
        $group->icon = $this->data['icon'];
        $group->is_display =(bool) $this->data['isDisplay'];
        $group->is_paid = (int)$this-> data['isPaid'];
        $group->default =(bool) $this->data['default'];
        $group->fee = (int)$this->data['fee'];
        $group->days =(int) $this->data['days'];
        $group->scale =(double) $this->data['scale'];
        $group->is_subordinate = (bool)$this->data['isSubordinate'];
        $group->is_commission =(bool) $this->data['isCommission'];

        if ($group->is_paid) {
            $fee = $this->data['fee'];
            $group->fee = sprintf('%.2f', $fee);
        }

        if ($group->is_paid) {
            $group->days = $this->data['days'];
        }

        $group->raise(new Created($group));

        $this->events->dispatch(
            new Saving($group, $this->actor, $this->data)
        );

        $group->save();

        AdminActionLog::createAdminActionLog(
            $this->actor->id,
            '新增用户角色【'. $group->name .'】'
        );

        $this->dispatchEventsFor($group, $this->actor);
        return $group;
    }
}
