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

use App\Models\User;
use Discuz\Console\AbstractCommand;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Expression;

class NicknameSetCommand extends AbstractCommand
{
    protected $signature = 'upgrade:nicknameSet';

    protected $description = '将空的用户昵称设置为用户名.';

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    protected $num = 1000; // 一次性能够批量处理的数据量

    protected $debugInfo = false;

    /**
     * AvatarCleanCommand constructor.
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    public function handle()
    {
        $this->info('脚本执行 [开始]');
        $this->info('');

        $count = User::query()->where('nickname', '=', '')->count();
        $bar = $this->createProgressBar($count);
        $bar->start();
        $this->info('');

        if ($count > 0) {
            User::query()->where('nickname', '=', '')->update(['nickname' => new Expression('username')]);
        } else {
            $this->info('当前要处理的数据为0');
        }

        $bar->finish();
        $this->info('');
        $this->info('脚本执行 [完成]');
    }
}
