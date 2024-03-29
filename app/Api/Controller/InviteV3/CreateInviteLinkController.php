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

namespace App\Api\Controller\InviteV3;

use App\Common\ResponseCode;
use App\Models\Group;
use App\Models\User;
use App\Models\Invite;
use Carbon\Carbon;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Support\Str;

class CreateInviteLinkController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$userRepo->canCreateInviteUserScale($this->user)) {
            throw new PermissionDeniedException('您没有权限邀请他人');
        }
        return true;
    }

    public function main()
    {
        if (!$this->user->isAdmin()) {
            $result = array(
            'userId' => $this->user->id,
            'code'   => $this->user->id
            );

            return $this->outPut(ResponseCode::SUCCESS, '', $result);
        }
        
        $group = Group::query()->where('default', 1)->first();
        $groupId = $group->id;

        $inviteGroup = $this->inPut('group');
        if ($inviteGroup) {
            $groupId = $inviteGroup;
        }

        $invite = new Invite();
        $invite->group_id = $groupId;
        $invite->type = Invite::TYPE_GENERAL;
        $invite->code = Str::random(Invite::INVITE_GROUP_LENGTH);
        $invite->dateline = Carbon::now()->timestamp;
        $invite->endtime = Carbon::now()->addDays(7)->timestamp;
        $invite->user_id = $this->user->id;

        $db = $this->getDB();
        $db->beginTransaction();
        try {
            $invite->save();
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            $this->info('createInviteLink_error_' . $this->user->id, $e->getMessage());
            $this->outPut(ResponseCode::DB_ERROR, $e->getMessage());
        }

        $result = array(
            'userId' => $invite->user_id,
            'code'   => $invite->code
        );

        return $this->outPut(ResponseCode::SUCCESS, '', $result);
    }
}
