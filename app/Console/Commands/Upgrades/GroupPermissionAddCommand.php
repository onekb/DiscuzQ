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

use App\Models\Group;
use App\Models\Permission;
use App\Models\Thread;
use Discuz\Console\AbstractCommand;
use Exception;
use Illuminate\Database\ConnectionInterface;

class GroupPermissionAddCommand extends AbstractCommand
{
    protected $signature = 'upgrade:groupPermissionAdd';

    protected $description = '追加内容付费场景用户组权限数据';

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * AvatarCleanCommand constructor.
     * @param string|null $name
     * @param ConnectionInterface $connection
     */
    public function __construct(string $name = null, ConnectionInterface $connection)
    {
        parent::__construct($name);

        $this->connection = $connection;
    }

    public function handle()
    {
        $this->info('脚本执行 [开始]');
        $this->info('');

        $newPermission = [
            'createThread.' . Thread::TYPE_OF_TEXT . '.position',
            'createThread.' . Thread::TYPE_OF_LONG . '.position',
            'createThread.' . Thread::TYPE_OF_VIDEO . '.position',
            'createThread.' . Thread::TYPE_OF_IMAGE . '.position',
            'createThread.' . Thread::TYPE_OF_AUDIO . '.position',
            'createThread.' . Thread::TYPE_OF_QUESTION . '.position',
            'createThread.' . Thread::TYPE_OF_GOODS . '.position'
        ];

        $permission = Permission::query()
            ->where('permission', 'like', 'createThread%')
            ->where('group_id', Group::MEMBER_ID)
            ->get();
        $permission = $permission->toArray();

        $existPermission = array();
        foreach ($permission as $permission_key => $permission_val) {
            array_push($existPermission ,$permission_val['permission']);
        }

        foreach ($newPermission as $key => $val) {
            $info = '数据 [group_id => '.Group::MEMBER_ID.',permission => '.$val.'] ';

            if (!in_array($val, $existPermission)) {
                $this->connection->beginTransaction();
                try {
                    Permission::query()->insert(['group_id' => Group::MEMBER_ID, 'permission' => $val]);

                    $this->info($info.'插入成功');

                    $this->connection->commit();
                } catch (Exception $e) {
                    app('log')->info($info.'插入异常：' . $e->getMessage());

                    $this->info($info.'插入异常');

                    $this->connection->rollback();
                }
            } else {
                $this->info($info.'已存在');
            }
        }

        $this->info('');
        $this->info('脚本执行 [完成]');
    }
}
