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

use App\Common\AuthUtils;
use App\Models\User;
use App\Models\UserWechat;
use App\Models\UserQq;
use Discuz\Console\AbstractCommand;
use Exception;

class UpdateUserBingType extends AbstractCommand
{
    protected $signature = 'upgrade:bindType';
    protected $description = '用户绑定枚举更新';

    protected function handle()
    {
        User::query()->where('mobile', '<>', '')->update(['bind_type' => AuthUtils::PHONE]);
        User::query()->whereIn('id', UserWechat::query()->get(['user_id']))->increment('bind_type', AuthUtils::WECHAT);
        User::query()->whereIn('id', UserQq::query()->get(['user_id']))->increment('bind_type', AuthUtils::QQ);
        $this->info('success');
    }
}
