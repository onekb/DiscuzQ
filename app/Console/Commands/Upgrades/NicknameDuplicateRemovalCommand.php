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

class NicknameDuplicateRemovalCommand extends AbstractCommand
{
    protected $signature = 'upgrade:nicknameDuplicateRemoval';
    protected $description = '将重复的昵称重新命名.';

    public function handle()
    {
        $this->info('脚本执行 [开始]');
        $this->info('');
        $count = 0;

        try {
            $userNickname = [];
            User::query()->select('id', 'nickname', 'created_at')
                ->orderBy('created_at','asc')
                ->get()->each(function ($item) use (&$userNickname, &$count) {
                    if (isset($userNickname[$item['nickname']])) {
                        if (strtotime($userNickname[$item['nickname']]['created_at']) > strtotime($item->created_at)) {
                            // 实则不走这段逻辑，排序异常才走
                            $userNickname[$item['nickname']]['nickname'] = User::addStringToNickname($userNickname[$item['nickname']]['nickname']);
                            $userNickname[$item['nickname']]->save();
                            $userNickname[$item['nickname']] = $item;
                        } else {
                            $item->nickname = User::addStringToNickname($item->nickname);
                            $item->save();
                        }
                        $count ++;
                    } else {
                        $userNickname[$item['nickname']] = $item;
                    }
                });
        } catch (\Exception $e) {
            app('log')->info('nickname_duplicate_removal_error::'.$e->getMessage());
        }

        $this->info('');
        $this->info('脚本执行 [完成],已处理'.$count.'条数据');
    }
}
