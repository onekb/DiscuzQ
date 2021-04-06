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

namespace App\Api\Controller\Posts;

use App\Models\Thread;
use App\Models\ThreadReward;
use App\Models\Post;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use App\Models\Order;
use Carbon\Carbon;
use App\Repositories\ThreadRewardRepository;
use Exception;
use Discuz\Http\DiscuzResponseFactory;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CreatePostRewardController implements RequestHandlerInterface
{

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct(ConnectionInterface $connection) {
        $this->connection = $connection;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $data = Arr::get($body, 'data', []);
        $attributes = Arr::get($data, 'attributes', []);
        $actor = $request->getAttribute('actor');
        $thread_id = Arr::get($attributes, 'thread_id');

        $checkRewards = preg_match('/^(([1-9][0-9]*)|(([0]\.\d{0,2}|[1-9][0-9]*\.\d{0,2})))$/', $attributes['rewards']);
        if(!$checkRewards){
            throw new Exception(trans('post.post_reward_type_error'));
        }

        $rewards = Arr::get($attributes, 'rewards', 0);
        $rewards = floatval(sprintf('%.2f', $rewards));

        if(empty($thread_id)){
            throw new Exception(trans('post.post_reward_does_not_have_thread_id'));
        }

        $threadReward = ThreadReward::query()->where('thread_id', $thread_id)->first();
        $remain_money = floatval(sprintf('%.2f', $threadReward['remain_money']));

        if(empty($threadReward)){
            throw new Exception(trans('post.post_reward_detail_not_found'));
        }

        if($threadReward['user_id'] !== $actor->id){
            throw new Exception(trans('post.post_reward_user_limit'));
        }

        if(Carbon::now() > $threadReward['expired_at']){
            throw new Exception(trans('post.post_reward_is_over'));
        }

        if($threadReward['remain_money'] == 0){
            throw new Exception(trans('post.post_reward_the_rewards_is_use_up'));
        }

        if($rewards <= 0){
            throw new Exception(trans('post.post_reward_the_rewards_limit_fail'));
        }

        if($remain_money < $rewards){
            throw new Exception(trans('post.post_reward_not_sufficient_funds'));
        }

        if(!isset($attributes['post_id']) || empty($attributes['post_id'])){
            throw new Exception(trans('post.post_reward_does_not_have_post_id'));
        }

        $threads = Thread::query()->where(['id' => $thread_id, 'is_approved' => 1])->whereNull('deleted_at')->first();
        if(empty($threads)){
            throw new Exception(trans('post.post_reward_thread_detail_not_found'));
        }

        $posts = Post::query()->where(['id' => $attributes['post_id'], 'thread_id' => $thread_id, 'is_comment' => 0])->whereNull('deleted_at')->first();
        if(empty($posts)){
            throw new Exception(trans('post.post_reward_post_detail_not_found'));
        }

        if($posts['user_id'] == $actor->id){
            throw new Exception(trans('post.post_reward_post_user_id_limit'));
        }

        $threadRewardOrder = Order::query()->where(['thread_id' => $thread_id, 'status' => Order::ORDER_STATUS_PAID])->first();
        if(empty($threadRewardOrder)){
            app('log')->info('获取不到悬赏帖订单信息，作者' . $actor->username . '悬赏采纳失败！;悬赏问答帖ID为：' . $thread_id);
            throw new Exception(trans('post.post_reward_order_error'));
        }

        // 订单实付金额 !== 悬赏帖金额，不允许进行采纳动作
        if($threadRewardOrder['amount'] !== $threadReward['money']){
            throw new Exception(trans('post.post_reward_not_equal_to_order_price'));
        }

        // 通过订单实付金额、用户钱包流水统计实际已悬赏的金额，获取真实的剩余金额
        $postRewardLog = UserWalletLog::query()->where('thread_id', $thread_id)->get()->toArray();
        $rewardTotal = 0;
        if(!empty($postRewardLog)){
            $rewardTotal = array_sum(array_column($postRewardLog, 'change_available_amount'));
        }
        $trueRemainMoney = $threadRewardOrder['amount'] - $rewardTotal;
        if($trueRemainMoney < $rewards){
            throw new Exception(trans('post.post_reward_not_sufficient_funds'));
        }

        $this->connection->beginTransaction();
        try {
            if($threadRewardOrder['payment_type'] == Order::PAYMENT_TYPE_WALLET){
                $userWallet = UserWallet::query()->lockForUpdate()->find($actor->id);
                if($userWallet->freeze_amount - $rewards < 0){
                    app('log')->info('作者' . $actor->username . '的冻结金额小于采纳金额，悬赏采纳失败！;悬赏问答帖ID为：' . $thread_id);
                    throw new Exception(trans('post.post_reward_user_wallet_error'));
                }else{
                    $userWallet->freeze_amount = $userWallet->freeze_amount - $rewards;
                    $userWallet->save();
                }
            }

            $postUserWallet = UserWallet::query()->lockForUpdate()->find($posts['user_id']);
            $postUserWallet->available_amount = $postUserWallet->available_amount + $rewards;
            $postUserWallet->save();

            $threadRewardRemainMoney = ThreadReward::query()->lockForUpdate()->find($threadReward['id']);
            $threadRewardRemainMoney->remain_money = $threadRewardRemainMoney->remain_money - $rewards;
            $threadRewardRemainMoney->save();

            UserWalletLog::createWalletLog(
                $posts['user_id'],
                $rewards,
                0,
                UserWalletLog::TYPE_INCOME_THREAD_REWARD,
                trans('wallet.income_thread_reward'),
                null,
                null,
                $actor->id,
                0,
                $attributes['post_id'],
                $thread_id
            );

            $this->connection->commit();

        } catch (Exception $e) {
            $this->connection->rollback();
            app('log')->info('作者' . $actor->username . '悬赏采纳失败，数据回滚！;悬赏问答帖ID为：' . $thread_id . ';异常错误记录：' . $e->getMessage());
            throw $e;
        }

        // 发送通知
        app(ThreadRewardRepository::class)->returnThreadRewardNotify($thread_id, $posts['user_id'], $rewards, UserWalletLog::TYPE_INCOME_THREAD_REWARD);
        return DiscuzResponseFactory::EmptyResponse(204);
    }
}