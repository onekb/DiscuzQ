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

namespace App\Console\Commands\Upgrades;

use App\Common\PermissionKey;
use App\Models\Group;
use App\Models\Permission;
use Discuz\Base\DzqLog;
use Discuz\Console\AbstractCommand;

class AddUnpaidPermissionCommand extends AbstractCommand
{
    protected $signature = 'upgrade:addUnpaidPermission';
    protected $description = '添加待付费用户组四个权限';

    public function handle()
    {
        $this->info('脚本执行 [开始]');
        $this->info('');

        try {
            Permission::query()->insertOrIgnore(
                [
                    ['group_id' => Group::UNPAID, 'permission' => PermissionKey::ORDER_CREATE],               // 创建订单
                    ['group_id' => Group::UNPAID, 'permission' => PermissionKey::TRADE_PAY_ORDER],            // 订单支付
                    ['group_id' => Group::UNPAID, 'permission' => PermissionKey::THREAD_INSERT_IMAGE],        // 插入图片
                    ['group_id' => Group::UNPAID, 'permission' => PermissionKey::THREAD_INSERT_ATTACHMENT]    // 插入附件
                ]
            );
        } catch (\Exception $e) {
            DzqLog::error('add_unpaid_permission_command_error', [], $e->getMessage());
            $this->info('脚本执行 [异常]');
        }

        $this->info('');
        $this->info('脚本执行 [完成]');
    }
}
