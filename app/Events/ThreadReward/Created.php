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

namespace App\Events\ThreadReward;

use App\Models\ThreadReward;
use App\Models\User;

class Created
{
    /**
     * @var ThreadReward
     */
    public $threadReward;

    /**
     * @var User
     */
    public $actor;

    /**
     * @var array
     */
    public $data;

    /**
     * Created constructor.
     *
     * @param ThreadReward $threadReward
     * @param null $actor
     * @param array $data
     */
    public function __construct(ThreadReward $threadReward, $actor = null, array $data = [])
    {
        $this->threadReward = $threadReward;
        $this->actor = $actor;
        $this->data = $data;
    }
}
