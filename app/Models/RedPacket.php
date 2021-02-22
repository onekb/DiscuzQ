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
use Discuz\Database\ScopeVisibilityTrait;
use Discuz\Foundation\EventGeneratorTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $thread_id
 * @property int $post_id
 * @property int $rule
 * @property int $condition
 * @property int $likenum
 * @property string $money
 * @property int $number
 * @property Carbon $created_at
 * @property Carbon $remain_money
 * @property Carbon $remain_number
 * @property Carbon $status
 * @package App\Models
 */
class RedPacket extends Model
{
    use EventGeneratorTrait;
    use ScopeVisibilityTrait;

    protected $table = 'thread_red_packets';

    /**
     * 红包状态
     */
    const RED_PACKET_STATUS_OVERDUE     = 0; //红包已过期(暂时无用)

    const RED_PACKET_STATUS_VALID       = 1; //红包未过期

    const RED_PACKET_STATUS_BROUGHT_OUT = 2; //红包已领完

    const RED_PACKET_STATUS_RETURN      = 3; //红包已退还

    const RED_PACKET_STATUS_UNTREATED   = 4; //不作处理的异常红包

    public static function creation(
        $thread_id,
        $post_id,
        $rule,
        $condition,
        $likenum,
        $money,
        $number,
        $remain_money,
        $remain_number,
        $status,
        $thread_red_packet
    ) {
        if (empty($thread_red_packet->id)) {
            $thread_red_packet = new static;
        }

        $thread_red_packet->thread_id = $thread_id;
        $thread_red_packet->post_id = $post_id;
        $thread_red_packet->rule = $rule;
        $thread_red_packet->condition = $condition;
        $thread_red_packet->likenum = $likenum;
        $thread_red_packet->money = $money;
        $thread_red_packet->number = $number;
        $thread_red_packet->remain_money = $remain_money;
        $thread_red_packet->remain_number = $remain_number;
        $thread_red_packet->status = $status;

        return $thread_red_packet;
    }

    /**
     * Create a new instance in reply to a thread.
     *
     * @param $threadId
     * @param $postId
     * @param $rule
     * @param $condition
     * @param $likenum
     * @param $mmoney
     * @param $number
     * @param $remainMoney
     * @param $leftNumber
     * @param $status
     * @return static
     */
    public static function reply($threadId, $postId, $rule, $condition, $likenum, $mmoney, $number, $remainMoney, $leftNumber, $status)
    {
        $redpacket = new static;
        $redpacket->thread_id = $threadId;
        $redpacket->post_id = $postId;
        $redpacket->rule = $rule;
        $redpacket->condition = $condition;
        $redpacket->likenum = $likenum;
        $redpacket->money = $mmoney;
        $redpacket->number = $number;
        $redpacket->created_at = Carbon::now();
        $redpacket->remain_money = $remainMoney;
        $redpacket->remain_number = $leftNumber;
        $redpacket->status = $status;

        return $redpacket;
    }

    /**
     * Define the relationship with the post's thread.
     *
     * @return BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Define the relationship with the post's like state for a particular user.
     *
     * @return BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

}
