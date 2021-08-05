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

namespace App\Api\Controller\GroupV3;

use App\Common\ResponseCode;
use App\Commands\Group\CreateGroup;
use App\Models\Group;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;

class CreateGroupController extends DzqController
{
    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $userRepo->canCreateGroup($this->user);
    }

    public function main()
    {
        $actor = $this->user;

        $group = [
            'id' => $this->inPut('id'),
            'name' => $this->inPut('name'),
            'type' => $this->inPut('type')  ,
            'color' => $this->inPut('color'),
            'icon' => $this->inPut('icon'),
            'default' => $this->inPut('default'),
            'isDisplay' => $this->inPut('isDisplay'),
            'isPaid' => $this->inPut('isPaid'),
            'fee' => $this->inPut('fee'),
            'days' => $this->inPut('days'),
            'scale' => $this->inPut('scale'),
            'isSubordinate' => $this->inPut('isSubordinate'),
            'isCommission' => $this->inPut('isCommission'),
        ];

       // dump($group);die;

        if(empty($group['name'])){
            $this->outPut(ResponseCode::INVALID_PARAMETER, '');
        }

        if(Group::query()->where('name',$group['name'])->first()){
            $this->outPut(ResponseCode::RESOURCE_EXIST, '');
        }

        $this->dzqValidate($group, [
            'name'=> 'required_without|max:200',
        ]);

        $result = $this->bus->dispatch(
            new CreateGroup($actor, $group)
        );
        $data = $this->camelData($result);
        return $this->outPut(ResponseCode::SUCCESS, '',$data);
    }

}
