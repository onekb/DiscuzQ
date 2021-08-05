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

use App\Models\StopWord;
use Discuz\Console\AbstractCommand;

class StopWordsNicknameSetCommand extends AbstractCommand
{
    protected $signature = 'upgrade:stopWordsNicknameSet';
    protected $description = '将敏感词表中空的 nickname 设置为忽略：{IGNORE}';

    public function handle()
    {
        $this->info('脚本执行 [开始]');
        $this->info('');

        $count = StopWord::query()->where('nickname', '=', '')->count();
        $bar = $this->createProgressBar($count);
        $bar->start();
        $this->info('');

        if ($count > 0) {
            StopWord::query()->where('nickname', '=', '')->update(['nickname' => '{IGNORE}']);
        }

        $bar->finish();
        $this->info('');
        $this->info('脚本执行 [完成],'.$count.'条数据已处理.');
    }
}
