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


use App\Models\Setting;
use Discuz\Console\AbstractCommand;

class ApiFreqCommand extends AbstractCommand
{
    protected $signature = 'api:freq';
    protected $description = '添加api限频配置';
    protected function handle()
    {
        $setting = Setting::query()->where('key', 'api_freq')->get();
        if ($setting->isEmpty()) {
            $setting = new Setting();
            $setting->setRawAttributes([
                'key' => 'api_freq',
                'value' => '{"get":{"freq":500,"forbidden":20},"post":{"freq":200,"forbidden":30}}',
                'tag' => 'default',
            ]);
            $info = $setting->save() ? '新增成功' : '新增失败';
        } else {
            $info = '不必添加';
        }
        $this->info('');
        $this->info($info);
    }
}
