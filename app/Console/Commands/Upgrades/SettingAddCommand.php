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
use App\Models\Setting;
use App\Models\Thread;
use Discuz\Console\AbstractCommand;
use Exception;
use Illuminate\Database\ConnectionInterface;

class SettingAddCommand extends AbstractCommand
{
    protected $signature = 'upgrade:settingAdd';

    protected $description = '追加内容付费场景网站设置数据';

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

        $newSetting = [
            'site_open_sort' => 0,
            'site_create_thread' . Thread::TYPE_OF_TEXT => 1,
            'site_create_thread' . Thread::TYPE_OF_LONG => 1,
            'site_create_thread' . Thread::TYPE_OF_VIDEO => 1,
            'site_create_thread' . Thread::TYPE_OF_IMAGE => 1,
            'site_create_thread' . Thread::TYPE_OF_AUDIO => 1,
            'site_create_thread' . Thread::TYPE_OF_QUESTION => 1,
            'site_create_thread' . Thread::TYPE_OF_GOODS => 1
        ];

        $setting = Setting::query()->get();
        $setting = $setting->toArray();

        $existSetting = array();
        foreach ($setting as $setting_key => $setting_val) {
            array_push($existSetting ,$setting_val['key']);
        }

        foreach ($newSetting as $key => $val) {
            $info = '数据 ['.$key.','.$val.']';

            if (!in_array($key, $existSetting)) {
                $this->connection->beginTransaction();
                try {
                    Setting::query()->insert(['key' => $key, 'value' => $val, 'tag' => 'default']);

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
