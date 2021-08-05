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

use App\Commands\Users\UpdateAdminUser;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;

class UpdateUsersStatusController extends DzqController
{

    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$userRepo->canUserStatus($this->user)) {
            throw new PermissionDeniedException('您没有审核权限');
        }
        return true;
    }

    public function main()
    {
        $log = app('adminLog');
        $actor = $this->user;
        $statusData = $this->inPut('data');
        $list = collect();

        foreach ($statusData as $data) {
            $id = Arr::get($data, 'id');
            $requestData = [];

            if(!empty($data['id'])){
                $requestData['id'] = $data['id'];
            }
            
            $requestData['status'] = $data['status'];

            if(!empty($data['rejectReason'])){
                $requestData['rejectReason'] = $data['rejectReason'];
            }

            try {
                $item = $this->bus->dispatch(
                    new UpdateAdminUser($id, $requestData, $actor)
                );
            } catch (\Exception $e) {
                $log->error('requestId：' . $this->requestId . '-' . '用户审核：入参：'
                    .';data:'.json_encode($requestData)
                    . ';异常：' . $e->getMessage());
                return $this->outPut(ResponseCode::INTERNAL_ERROR, '用户审核接口异常');
            }
            $list->push($item);
        }

        $data = [];
        foreach ($list as $lists) {
            $data [] = [
                'id' => $lists['id'],
                'status' => $lists['status'],
                'rejectReason' => $lists['reject_reason'],
            ];
        }

        return $this->outPut(ResponseCode::SUCCESS,'', $data);
    }
}
