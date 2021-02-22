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

namespace App\Models;

use App\Events\AdminActionLog\Created;
use Carbon\Carbon;
use Discuz\Database\ScopeVisibilityTrait;
use Discuz\Foundation\EventGeneratorTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $user_id
 * @property string $action_desc
 * @property string $ip
 * @property Carbon $created_at
 */
class AdminActionLog extends Model
{
    use EventGeneratorTrait;
    use ScopeVisibilityTrait;

    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $table = 'admin_action_logs';

    /**
     * Create a new adminactionlog.
     *
     * @param string $user_id
     * @param string $action_desc
     * @param string $ip
     * @return static
     */
    public static function createAdminActionLog($user_id, $action_desc)
    {
        $request = app('request');
        $adminactionlog = new static;

        $adminactionlog->user_id = $user_id;
        $adminactionlog->action_desc = $action_desc;
        $adminactionlog->ip = ip($request->getServerParams());
        $adminactionlog->created_at = Carbon::now();

        $adminactionlog->save();

        $adminactionlog->raise(new Created($adminactionlog));

        return $adminactionlog;
    }
}
