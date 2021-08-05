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

use App\Models\Group;
use App\Common\ResponseCode;
use App\Models\Sequence;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ListGroupsController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $userRepo->canListGroup($this->user);
    }

    public function main()
    {
        $include = $this->inPut('include');
        $filter = $this->inPut('filter');

        $type = Arr::get($filter, 'type', '');
        $isPaid = Arr::get($filter, 'isPaid', '');
        $isDefault = (bool) Arr::get($filter, 'isDefault', false);

        if(!empty($isPaid)){
            $this->dzqValidate($filter, [
                'isPaid' => 'integer|in:0,1'
            ]);
        }
        if(!empty($isDefault)){
            $this->dzqValidate($filter, [
                'isDefault' => 'integer'
            ]);
        }

        $groups = Group::query()
            ->where('id', '<>', Group::UNPAID)
            ->when($isDefault, function (Builder $query) {
                return $query->where('default', true);
            })
            ->when($isPaid != '', function (Builder $query) use ($isPaid) {
                return $query->where('is_paid', (bool) $isPaid);
            })
            ->when($type === 'invite', function (Builder $query) use ($include) {
                // 邀请用户组关联权限不返回 分类下权限
                if (in_array('permission', $include)) {
                    $query->with([
                        'permission' => function ($query) {
                            $query->where('permission', 'not like', 'category%')
                                ->where('permission', 'not like', 'switch.%');
                        },
                    ]);
                }
                // 不返回游客用户组
                return $query->where('id', '<>', Group::GUEST_ID);
            });
        $data = [];
        foreach ($groups->get() as $lists) {
            $data [] = [
                'id' => $lists['id'],
                'name' => $lists['name'],
                'type' => $lists['type'],
                'default' => $lists['default'],
                'isDisplay' => $lists['is_display'],
                'isPaid' => $lists['is_paid'],
                'fee' => $lists['fee'],
                'days' => $lists['days'],
                'scale' => $lists['scale'],
                'isSubordinate' => $lists['is_subordinate'],
                'isCommission' => $lists['is_commission'],
                'checked'           => in_array($lists['id'], $this->getCheckList()) ? 1 : 0
            ];
        }
        $data = $this->camelData($data);

       return $this->outPut(ResponseCode::SUCCESS, '',$data);
    }


    public function getCheckList(){
        $groupsList = Sequence::query()->first();
        if (empty($groupsList)) return [];
        $groupsList = explode(',',$groupsList['group_ids']);
        return $groupsList;
    }

}
