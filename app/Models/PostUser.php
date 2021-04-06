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

use Carbon\Carbon;
use Discuz\Base\DzqModel;

/**
 * Models a post-user state record in the database.
 *
 * @property int $user_id
 * @property int $thread_id
 * @property Carbon|null $created_at
 * @property Thread $thread
 * @property User $user
 */
class PostUser extends DzqModel
{
    protected $table = 'post_user';
    public function likedUsers()
    {
        return $this->hasOne(User::class);
    }
    public function getPostIdsByUid($postIds,$userId){
        return  self::query()->whereIn('post_id',$postIds)->where('user_id',$userId)
            ->get()->pluck('post_id')->toArray();
    }
}
