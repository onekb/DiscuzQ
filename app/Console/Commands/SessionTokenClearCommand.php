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

namespace App\Console\Commands;

use App\Models\SessionToken;
use Carbon\Carbon;
use Discuz\Console\AbstractCommand;

class SessionTokenClearCommand extends AbstractCommand
{
    protected $signature = 'clear:session_token';

    protected $description = '清理过期 session token';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $count = SessionToken::query()->where('expired_at', '<', Carbon::now())->delete();
        $this->info('清理过期 session token 数量：'. $count);
    }
}
