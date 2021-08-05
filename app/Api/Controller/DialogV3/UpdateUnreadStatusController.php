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

namespace App\Api\Controller\DialogV3;

use App\Models\DialogMessage;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\NotAuthenticatedException;
use Discuz\Base\DzqController;

class UpdateUnreadStatusController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $actor = $this->user;

        $dialogId = $this->inPut('dialogId');

        if(empty($dialogId)) {
            return $this->outPut(ResponseCode::INVALID_PARAMETER);
        }

        $dialogList = DialogMessage::query()
            ->where('dialog_id', $dialogId)
            ->where('user_id','!=',$actor->id)
            ->where('status', DialogMessage::NORMAL_MESSAGE)
            ->get(['id','read_status']);

        foreach ($dialogList as $value) {
            $value->read_status = 1;
            $value->save();
        }

        return $this->outPut(ResponseCode::SUCCESS,'');
    }

}
