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

namespace App\Commands\RedPacket;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use App\Models\RedPacket;
use App\Models\UserWalletLog;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Foundation\EventsDispatchTrait;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Database\ConnectionInterface;

/**
 * @property User threadUser
 * @property User beLikeUser
 * @property User clickLikeUser
 * @property Post post
 * @property ConnectionInterface connection
 * @property BusDispatcher bus
 */
class CountLikedMakeRedPacket
{
    use AssertPermissionTrait;
    use EventsDispatchTrait;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes of the new thread.
     *
     * @var array
     */
    public $data;

    /**
     * The current ip address of the actor.
     *
     * @var array
     */
    public $ip;

    /**
     * The current port of the actor.
     *
     * @var int
     */
    public $port;

    public $baseInfo = '';

    public $debugInfo = false; // false:默认不输出调试信息到日志上

    protected $connection;

    /**
     * @param User $threadUser      //当前帖子用户
     * @param User $beLikeUser      //被点赞用户
     * @param User $clickLikeUser   //点赞用户
     * @param Post $post            //被点赞的回复
     */
    public function __construct(User $threadUser,User $beLikeUser,User $clickLikeUser,Post $post)
    {
        $this->threadUser = $threadUser;
        $this->beLikeUser = $beLikeUser;
        $this->clickLikeUser = $clickLikeUser;
        $this->post = $post;

    }

    /**
     * @param EventDispatcher $events
     * @param ConnectionInterface $connection
     * @param BusDispatcher $bus
     * @return void
     * @throws Exception
     * @throws \App\Exceptions\TradeErrorException
     */
    public function handle(EventDispatcher $events, ConnectionInterface $connection, BusDispatcher $bus)
    {
        $this->events = $events;
        $this->connection = $connection;
        $this->bus = $bus;
        $thread = $this->post->thread->getAttributes();
        $post = $this->post->getAttributes();
        $type = $thread['type'];
        $this->baseInfo =   '点赞用户id:'       . $this->clickLikeUser->id.
                            ',被点赞用户id:'    . $this->beLikeUser->id.
                            ',帖子所属用户id: ' . $this->threadUser->id.
                            ',访问帖子id:'      . $this->post->thread->id.
                            ',post_id:'        . $this->post->id.
                            ',msg:';

        if (!($type == Thread::TYPE_OF_TEXT || $type == Thread::TYPE_OF_LONG)) {
            $this->outDebugInfo('点赞领红包：该帖不为文字帖和长文贴');
            return;
        }

        if ($thread['is_red_packet'] != Thread::HAVE_RED_PACKET
            || $post['is_first'] == 1
            || $post['is_comment'] == 1
        ) {
            $this->outDebugInfo('点赞领红包：该帖不为红包帖、首帖、第一条评论');
            return;
        }

        $redPacket = RedPacket::query() ->where(['thread_id' => $thread['id'], 'status' => RedPacket::RED_PACKET_STATUS_VALID, 'condition' => 1])
                                        ->first();
        if (empty($redPacket) || empty($redPacket['remain_money']) || empty($redPacket['remain_number'])) {
            $this->outDebugInfo('点赞领红包：该红包帖无剩余金额和个数');
            return;
        }

        if ($post['like_count'] < $redPacket['likenum']) {
            $this->outDebugInfo('点赞领红包：该帖未达到集赞数');
            return;
        }

        //领取过红包的用户不再领取
        if ($thread['type'] == Thread::TYPE_OF_TEXT) {
            $change_type = UserWalletLog::TYPE_INCOME_TEXT;
        } else {
            $change_type = UserWalletLog::TYPE_INCOME_LONG;
        }
        $isReceive = UserWalletLog::query()->where([
            'user_id'       => $this->beLikeUser['id'],
            'change_type'   => $change_type,
            'thread_id'     => $thread['id']
        ])->first();
        if (!empty($isReceive)) {
            $this->outDebugInfo('点赞领红包：该用户已经领取过红包了');
            return;
        }

        $this->bus->dispatch(new ReceiveRedPacket($thread,$post,$redPacket,$this->threadUser,$this->beLikeUser));

    }

    public function outDebugInfo($info)
    {
        if ($this->debugInfo) {
            app('log')->info($this->baseInfo . $info);
        }
    }
}
