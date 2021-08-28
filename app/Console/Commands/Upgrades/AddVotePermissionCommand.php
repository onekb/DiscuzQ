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
use Discuz\Console\AbstractCommand;
use Illuminate\Database\ConnectionInterface;

class AddVotePermissionCommand extends AbstractCommand
{
    protected $signature = 'upgrade:addVotePermission';

    protected $description = '增加投票权限';

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

        $group_ids = Group::query()->whereNotIn('id',[Group::ADMINISTRATOR_ID, Group::GUEST_ID])->pluck('id')->toArray();
        $insert_group_permission = [];
        foreach ($group_ids as $val){
            $insert_group_permission[] = [
                'group_id'  =>  $val,
                'permission'    =>  'thread.insertVote'
            ];
        }
        $res = Permission::query()->insert($insert_group_permission);
        if($res === false){
            app('log')->error('新增投票权限出错');
            $this->info('');
            $this->info('插入投票权限出错');
        }else{
            $this->info('');
            $this->info('脚本执行 [完成]');
        }


    }
}


