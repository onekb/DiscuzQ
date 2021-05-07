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

namespace App\Repositories;

use App\Models\Thread;
use App\Models\ThreadReward;
use App\Models\User;
use App\Models\Order;
use App\Models\Post;
use App\Api\Serializer\ThreadSerializer;
use App\Api\Serializer\UserSerializer;
use Carbon\Carbon;
use Discuz\Foundation\AbstractRepository;
use Illuminate\Support\Arr;
use App\Notifications\ThreadRewarded;
use App\Notifications\ThreadRewardedExpired;
use App\Notifications\Messages\Wechat\ThreadRewardedWechatMessage;
use App\Notifications\Messages\Wechat\ThreadRewardedExpiredWechatMessage;
use Tobscure\JsonApi\Relationship;
use Discuz\Api\Serializer\AbstractSerializer;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Database\Eloquent\Model;

class ThreadRewardRepository extends AbstractRepository
{
    /**
     * Find a thread by ID, optionally making sure it is visible to a
     * certain user, or throw an exception.
     *
     * @param int $id
     * @param User|null $actor
     * @return Thread
     */
    public function returnThreadRewardNotify($thread_id, $user_id, $rewards, $type)
    {
        $query = Thread::query();
        $query->where(['id' => $thread_id]);
        $thread = $query->first();

        // 查悬赏过期信息
        $threadReward = ThreadReward::query()->where('thread_id', $thread_id)->first();
        $order = Order::query()->where(['thread_id' => $thread_id])->first();
        $actorUser = User::query()->where(['id' => $thread->user_id])->first();
        $user = User::query()->where(['id' => $user_id])->first();
        $orderArr = empty($order) ? array() : $order->toArray();

        if(!empty($thread)){
            $threadContent = $thread->title;
            if (empty($thread->title)) {
                $post = Post::query()->where(['thread_id' => $thread_id, 'is_first' => 1])->first();
                $threadContent = $post->content;
            }
        }else{
            $threadContent = '悬赏帖已过期且已被删除，返回冻结金额';
        }

        if(!empty($actorUser) && !empty($user)){
            $build = [
                'message' => $threadContent,
                'raw' => array_merge(Arr::only($orderArr, ['id', 'thread_id', 'type']), [
                    'actor_username' => $actorUser->username,   // 发送人姓名
                    'actual_amount' => $rewards,     // 获取作者实际金额
                    'content' => $threadContent,
                    'created_at' => (string)$thread->created_at
                ]),
            ];

            $walletType = $type;
            if(Carbon::now() > $threadReward['expired_at']){
                $user->notify(new ThreadRewardedExpired($user, $order, $build, $walletType));
            }else{
                $user->notify(new ThreadRewarded($user, $order, $build, $walletType));
            }
        }else{
            app('log')->info('过期悬赏发送错误：悬赏帖(ID为' . $thread_id . ')，因查询不到用户信息(ID为' . $user_id . ')，无法发送通知');
        }
    }
}
