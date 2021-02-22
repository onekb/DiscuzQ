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

namespace App\Listeners\RedPacket;

use App\Events\Post\Saved;
use App\Traits\PostNoticesTrait;
use Discuz\Auth\AssertPermissionTrait;
use Illuminate\Contracts\Events\Dispatcher;

class RedPacketListener
{
    use AssertPermissionTrait;
    use PostNoticesTrait;

    public function subscribe(Dispatcher $events)
    {

        //创建红包保存到数据库->冻结红包资金->更改钱包明细
        $events->listen(Saved::class, SaveRedPacketToDatabase::class);
        //回复领红包
        $events->listen(Saved::class, ReplyPostMakeRedPacket::class);

    }



}
