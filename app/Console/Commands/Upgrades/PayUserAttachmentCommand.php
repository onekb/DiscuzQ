<?php
/**
 * Copyright (C) 2021 Tencent Cloud.
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

namespace App\Console\Commands\Upgrades;


use App\Models\Group;
use App\Models\Permission;
use Discuz\Console\AbstractCommand;

class PayUserAttachmentCommand extends AbstractCommand
{
    protected $signature = 'unpay:attachment';
    protected $description = '待支付用户添加上传权限';

    protected function handle()
    {
        $a0 = Permission::query()->where(['group_id' => Group::UNPAID, 'permission' => 'attachment.create.0'])->first();
        if (empty($a0)) {
            $p0 = new Permission();
            $p0->group_id = Group::UNPAID;
            $p0->permission = 'attachment.create.0';
            $p0->save();
        }
        $a1 = Permission::query()->where(['group_id' => Group::UNPAID, 'permission' => 'attachment.create.1'])->first();
        if (empty($a1)) {
            $p1 = new Permission();
            $p1->group_id = Group::UNPAID;
            $p1->permission = 'attachment.create.1';
            $p1->save();
        }
    }
}
