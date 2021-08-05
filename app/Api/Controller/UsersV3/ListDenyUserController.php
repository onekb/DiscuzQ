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

namespace App\Api\Controller\UsersV3;

use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use App\Models\User;
use Discuz\Base\DzqController;

class ListDenyUserController extends DzqController
{

    private $databaseField = [
        'id',
        'updated_at',
        'created_at'
    ];


    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $actor = $this->user;
        $currentPage = $this->inPut('page');
        $perPage = $this->inPut('perPage');

        $query = User::query();
        $query->leftJoin('deny_users', 'id', '=', 'deny_user_id')
            ->where('user_id', $actor->id);
        $query->select('deny_users.user_id','deny_users.deny_user_id','users.id AS pid', 'users.username','users.nickname', 'users.avatar');
        $query->orderByDesc('deny_users.created_at');
        $users = $this->pagination($currentPage, $perPage, $query);
        $data = $this->camelData($users);

        return $this->outPut(ResponseCode::SUCCESS,'', $data);
    }
}
