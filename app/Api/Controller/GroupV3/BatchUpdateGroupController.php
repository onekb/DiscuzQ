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

use Discuz\Base\DzqController;
use App\Common\ResponseCode;
use Discuz\Auth\AssertPermissionTrait;
use App\Models\Group;
use App\Models\Invite;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Validation\Factory;

class BatchUpdateGroupController extends DzqController
{
    use AssertPermissionTrait;

    protected $validation;

    protected $bus;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$userRepo->canEditGroup($this->user)) {
            throw new PermissionDeniedException('您没有修改用户组的权限');
        }
        return true;
    }

    public function __construct(Dispatcher $bus, Factory $validation)
    {
        $this->validation = $validation;
        $this->bus = $bus;
    }

    public function main()
    {
        $data = $this->inPut('data');
        $this->assertBatchData($data);

        $resultData = [];
        if(empty($data)) {
            return $this->outPut(ResponseCode::INVALID_PARAMETER);
        }
        foreach ($data as $k=>$val) {
            $this->dzqValidate($val, [
                'name'=> 'required_without|max:200',
            ]);
            $groupData = Group::query()->where('id', $val['id'])->first();
            if(!$groupData){
                return $this->outPut(ResponseCode::INVALID_PARAMETER);
            }
        }

        foreach ($data as $value) {
            try {
                $group = Group::query()->findOrFail($value['id']);
                $group->name      = $value['name'];
                if(isset($value['type'])){
                    $group->type = $value['type'];
                }

                if(isset($value['isPaid'])){
                    $group->is_paid = $value['isPaid'];
                }

                if(isset($value['scale'])){
                    $group->scale = $value['scale'];
                }

                if(isset($value['isSubordinate'])){
                    $group->is_subordinate =(bool) $value['isSubordinate'];
                }

                if(isset($value['isCommission'])){
                    $group->is_commission = (bool) $value['isCommission'];
                }

                if(isset($value['isDisplay'])){
                    $group->is_display = (bool) $value['isDisplay'];
                }

                if(isset($value['default'])){
                   $group->default = (bool) $value['default'];
                    if ($value['default']) {
                        $changeInviteGroupResult = $this->changeInviteGroup($value['id']);
                    }
                }

                $group->save();
                $resultData[] = $group;
            } catch (\Exception $e) {
                $this->outPut(ResponseCode::DB_ERROR, '用户组修改失败', '');
                $this->info('用户组修改失败：' . $e->getMessage());
            }
        }

        $data = $this->camelData($resultData);
        return $this->outPut(ResponseCode::SUCCESS, '', $data);
    }

    public function changeInviteGroup($groupId)
    {
        $unusedInviteLinkList = Invite::query()
            ->where('group_id', '!=', $groupId)
            ->where('status', Invite::STATUS_UNUSED)
            ->where('endtime', '>', time())
            ->get();

        $unusedInviteLinkList->map(function ($item) use ($groupId) {
            try{
                $item->group_id = $groupId;
                $item->save();
            } catch (\Exception $e) {
                $this->outPut(ResponseCode::DB_ERROR, '相关邀请链接修改失败', '');
                $this->info('相关邀请链接修改失败：' . $e->getMessage());
            }
        });
    }
}
