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

namespace App\Api\Controller\PostsV3;

use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\Order;
use App\Models\OrderChildren;
use App\Models\Post;
use App\Models\Thread;
use App\Models\ThreadReward;
use App\Models\UserWallet;
use App\Models\UserWalletLog;
use App\Repositories\ThreadRewardRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Discuz\Base\DzqController;
use Exception;
use Illuminate\Database\ConnectionInterface;
use Discuz\Base\DzqCache;


class CreatePostRewardController extends DzqController
{
    protected $connection;

    public function __construct(ConnectionInterface $connection) {
        $this->connection = $connection;
    }

    public function prefixClearCache($user)
    {
        $threadId = $this->inPut('threadId');
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_THREADS, $threadId);
    }


    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $thread = Thread::query()->where(['id' => $this->inPut('threadId'), 'is_approved' => 1])->whereNull('deleted_at')->first();
        if (!$thread) {
            return false;
        }

        return $userRepo->canViewThreadDetail($this->user, $thread);
    }

    public function main()
    {
        $thread_id = $this->inPut("threadId");
        $rewards = $this->inPut("rewards") ? $this->inPut("rewards") : 0;
        $post_id = $this->inPut("postId");

        $attributes = [
            'thread_id'=>$thread_id,
            'rewards'=>$rewards,
            'post_id'=>$post_id,
        ];

        $actor = $this->user;
        $rewards = floatval(sprintf('%.2f', $rewards));

        $checkRewards = preg_match('/^(([1-9][0-9]*)|(([0]\.\d{0,2}|[1-9][0-9]*\.\d{0,2})))$/', $attributes['rewards']);
        if(!$checkRewards){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_type_error'));
        }

        if(empty($thread_id)){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_does_not_have_thread_id'));
        }

        $threadReward = ThreadReward::query()->where('thread_id', $thread_id)->first();
        $remain_money = floatval(sprintf('%.2f', $threadReward['remain_money']));

        if(empty($threadReward)){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_detail_not_found'));
        }

        if($threadReward['user_id'] !== $actor->id){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_user_limit'));
        }

        if(Carbon::now() > $threadReward['expired_at']){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_is_over'));
        }

        if($threadReward['remain_money'] == 0){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_the_rewards_is_use_up'));
        }

        if($rewards <= 0){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_the_rewards_limit_fail'));
        }

        if($remain_money < $rewards){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_not_sufficient_funds'));
        }

        if(!isset($attributes['post_id']) || empty($attributes['post_id'])){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_does_not_have_post_id'));
        }

        $posts = Post::query()->where(['id' => $attributes['post_id'], 'thread_id' => $thread_id, 'is_comment' => 0])->whereNull('deleted_at')->first();
        if(empty($posts)){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_post_detail_not_found'));
        }

        if($posts['user_id'] == $actor->id){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_post_user_id_limit'));
        }

        $threadRewardOrder = Order::query()->where(['thread_id' => $thread_id, 'status' => Order::ORDER_STATUS_PAID])->first();
        //合并订单处理
        if ($threadRewardOrder->type == Order::ORDER_TYPE_MERGE) {
            $OrderChildren = OrderChildren::query()
                ->where(['order_sn' => $threadRewardOrder['order_sn'], "type" => Order::ORDER_TYPE_QUESTION_REWARD ,'status' => Order::ORDER_STATUS_PAID])
                ->first(['amount']);
            $threadRewardOrder['amount'] = isset($OrderChildren['amount']) ? $OrderChildren['amount'] : 0;
        }
        if(empty($threadRewardOrder)){
            app('log')->info('获取不到悬赏帖订单信息，作者' . $actor->username . '悬赏采纳失败！;悬赏问答帖ID为：' . $thread_id);
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_order_error'));
        }

        // 订单实付金额 !== 悬赏帖金额，不允许进行采纳动作
        if($threadRewardOrder['amount'] !== $threadReward['money']){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_not_equal_to_order_price'));
        }

        // 通过订单实付金额、用户钱包流水统计实际已悬赏的金额，获取真实的剩余金额
        $postRewardLog = UserWalletLog::query()->where(['thread_id' => $thread_id ,'change_type' => UserWalletLog::TYPE_INCOME_THREAD_REWARD])->get()->toArray();
        $rewardTotal = 0;
        if(!empty($postRewardLog)){
            $rewardTotal = array_sum(array_column($postRewardLog, 'change_available_amount'));
        }
        $trueRemainMoney = floatval(sprintf('%.2f', $threadRewardOrder['amount'] - $rewardTotal));
        if($trueRemainMoney < $rewards){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_not_sufficient_funds'));
        }

        $this->connection->beginTransaction();
        try {
            if($threadRewardOrder['payment_type'] == Order::PAYMENT_TYPE_WALLET){
                $userWallet = UserWallet::query()->lockForUpdate()->find($actor->id);
                if($userWallet->freeze_amount - $rewards < 0){
                    app('log')->info('作者' . $actor->username . '的冻结金额小于采纳金额，悬赏采纳失败！;悬赏问答帖ID为：' . $thread_id);
                    return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_reward_user_wallet_error'));
                }else{
                    $userWalletUpdateResult = UserWallet::query()->where('user_id', $actor->id)->update(['freeze_amount' => $userWallet->freeze_amount - $rewards]);

                    // 流水-减少冻结
                    UserWalletLog::createWalletLog(
                        $userWallet->user_id,
                        0,
                        -$rewards,
                        UserWalletLog::TYPE_QUESTION_REWARD_EXPEND,
                        trans('wallet.question_reward_expend'),
                        null,
                        null,
                        0,
                        0,
                        $attributes['post_id'],
                        $thread_id
                    );
                }
            }

            $postUserWallet = UserWallet::query()->lockForUpdate()->find($posts['user_id']);
            $postUserWalletUpdateResult = UserWallet::query()->where('user_id', $posts['user_id'])->update(['available_amount' => $postUserWallet->available_amount + $rewards]);

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
        return $this->outPut(ResponseCode::SUCCESS);

    }
}
