<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *   http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Listeners\Question;

use App\Events\Post\Saved;
use App\Models\Order;
use App\Models\Question;
use App\Models\Thread;
use App\Models\UserWalletLog;
use App\Models\ThreadReward;
use App\Validators\QuestionValidator;
use Carbon\Carbon;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Foundation\EventsDispatchTrait;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class SaveQuestionToDatabase
{
    use EventsDispatchTrait;

    /**
     * @var QuestionValidator
     */
    protected $questionValidator;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var SettingsRepository
     */
    protected $settings;

    /**
     * @var BusDispatcher
     */
    protected $bus;

    public function __construct(
        EventDispatcher $eventDispatcher,
        QuestionValidator $questionValidator,
        ConnectionInterface $connection,
        SettingsRepository $settings,
        BusDispatcher $bus
    )
    {
        $this->events = $eventDispatcher;
        $this->questionValidator = $questionValidator;
        $this->connection = $connection;
        $this->settings = $settings;
        $this->bus = $bus;
    }

    /**
     * @param Saved $event
     * @throws ValidationException
     * @throws Exception
     */
    public function handle(Saved $event)
    {
        $post = $event->post;
        $actor = $event->actor;
        $data = $event->data;

        if ($post->thread->type == Thread::TYPE_OF_QUESTION) {

            $isDraft = Arr::get($data, 'attributes.is_draft');
            $isLikeData = Arr::get($data, 'attributes');

            // 判断是否是创建
            if ($post->is_first && !isset($isLikeData['isLiked'])) {
                $questionData = Arr::get($data, 'relationships.question.data');
                if (!$isDraft) {
                    if (empty($questionData)) {
                        throw new Exception(trans('post.post_question_missing_parameter')); // 问答缺失参数
                    }

                    if (! empty($orderId = Arr::get($questionData, 'order_id', null))) {
                        $orderData = Order::query()->where('order_sn', $orderId)->firstOrFail();
                        if($orderData->amount > 0 && $orderData->status !== 1){
                            throw new Exception(trans('post.post_question_order_pay_status_fail'));
                        }

                        if($questionData['price'] != $orderData->amount){
                            $questionData['price'] = $orderData->amount;
                            app('log')->info('用户'.$actor->username . '(ID为' . $actor->id . ')存在拦截请求、篡改数据行为，金额传参与实付金额不匹配。订单ID为：' . $orderData->order_sn . ',帖子ID为：' . $post->thread_id);
                        }
                    }

                    if(!isset($questionData['type'])){
                        $questionData['type'] = 1;
                        // throw new Exception(trans('post.post_reward_does_not_have_type'));
                    }

                    if($questionData['type'] == 1){
                        if(!isset($questionData['be_user_id']) || empty($questionData['be_user_id'])){
                            throw new Exception(trans('post.thread_reward_answer_id_is_null'));
                        }
                    }

                    if(isset($questionData['type']) && $questionData['type'] == 0){
                        // reward thread
                        if(!is_numeric($questionData['price'])){
                            throw new Exception(trans('post.thread_reward_money_type_fail'));
                        }

                        if($questionData['price'] < 0.1){
                            throw new Exception(trans('post.thread_reward_money_min_limit_fail'));
                        }

                        if($questionData['price'] > 10000){
                            throw new Exception(trans('post.thread_reward_money_max_limit_fail'));
                        }

                        if(!isset($questionData['expired_at']) || empty($questionData['expired_at'])){
                            throw new Exception(trans('post.thread_reward_expired_time_is_null'));
                        }

                        $min_time = date("Y-m-d H:i:s", strtotime("+1 days",time()));
                        if($questionData['expired_at'] < $min_time){
                            throw new Exception(trans('post.thread_reward_expired_time_limit_fail'));
                        }
                    }
                }

                /**
                 * Validator
                 *
                 * @see QuestionValidator
                 */
                $questionData['actor'] = $actor;
                if (!$isDraft) {
                    if (! isset($questionData['order_id']) || empty($questionData['order_id'])) {
                        $price = 0;
                    } else {
                        $price = $orderData->amount;
                    }
                }else{
                     $price = Arr::get($questionData, 'price', 0);
                }

                if($questionData['type'] == 1 && !$isDraft) {
                    $this->questionValidator->valid($questionData);
                }

                $isOnlooker = Arr::get($questionData, 'is_onlooker', true); // 获取帖子是否允许围观

                // get unit price
                $siteOnlookerPrice = (float) $this->settings->get('site_onlooker_price', 'default', 0);
                if ($siteOnlookerPrice > 0 && $isOnlooker) {
                    $onlookerUnitPrice = $siteOnlookerPrice;
                }

                // Start Transaction
                $this->connection->beginTransaction();
                try {

                    if($questionData['type'] == 1){
                        /**
                         * Create Question
                         *
                         * @var Question $question
                         */
                        $build = [
                            'thread_id' => $post->thread_id,
                            'user_id' => $actor->id,
                            'be_user_id' => Arr::get($questionData, 'be_user_id'),
                            'price' => $price,
                            'onlooker_unit_price' => $onlookerUnitPrice ?? 0,
                            'is_onlooker' => $actor->can('canBeOnlooker') ? $isOnlooker : false,
                            'expired_at' => Carbon::today()->addDays(Question::EXPIRED_DAY),
                        ];
                        $question = Question::query()->updateOrCreate(['thread_id'=> $post->thread_id], $build);
                        $questionId = $question->id;
                        if ($isDraft == 0) {
                            $question = Question::build($build);
                        }
                    }

                    if(empty($question)){
                        $answer_id = 0;
                    }else{
                        $answer_id = $question->be_user_id ? $question->be_user_id : 0;
                    }

                    $threadRewardData = [
                        'thread_id' => $post->thread_id,
                        'post_id' => $post->id,
                        'type' => $questionData['type'],
                        'user_id' => $actor->id,
                        'answer_id' => $answer_id,
                        'money' => $price,
                        'remain_money' => $price,
                        'created_time' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time()),
                        'expired_at' => isset($questionData['expired_at']) && !empty($questionData['expired_at']) ? $questionData['expired_at'] : Carbon::today()->addDays(Question::EXPIRED_DAY)
                    ];

                    $threadReward = ThreadReward::query()->updateOrCreate(['thread_id' => $post->thread_id, 'post_id' => $post->id], $threadRewardData);
                    if ($isDraft == 0) {
                        $threadReward = ThreadReward::build($threadRewardData);
                    }

                    // 判断如果没有传 order_id 说明是0元提问，就不需要冻结钱包
                    if (! empty($orderSn = Arr::get($questionData, 'order_id', null))) {
                        /**
                         * Update Order relation thread_id
                         *
                         * @var Order $order
                         */
                        $order = Order::query()->where('order_sn', $orderSn)->firstOrFail();
                        $order->thread_id = $post->thread_id;
                        if (isset($questionData['type']) && $questionData['type'] == 0) {
                            $order->master_amount = 0;
                            $order->author_amount = 0;
                        }
                        $order->save();

                        /**
                         * Update WalletLog relation question_id
                         *
                         * @var Order $order
                         * @var UserWalletLog $walletLog
                         */
                        if ($order->payment_type == Order::PAYMENT_TYPE_WALLET) {
                            $walletLog = UserWalletLog::query()->where([
                                'user_id'     => $actor->id,
                                'order_id'    => $order->id,
                                'change_type' => UserWalletLog::TYPE_QUESTION_FREEZE,
                            ])->first();

                                // question thread type = 1 for a specific people
                                if($questionData['type'] == 1){
                                    $walletLog->question_id = $questionId;
                                }

                                $walletLog->save();
                            }
                        }

                    $this->connection->commit();
                } catch (Exception $e) {
                    $this->connection->rollback();
                    app('log')->info('用户'.$actor->username.'创建问答帖失败，数据回滚！异常订单ID为：' . $order->id . ';异常错误记录：' . $e->getMessage());
                    throw $e;
                }

                // 延迟执行事件
                if(!empty($question)){
                    $this->dispatchEventsFor($question, $actor);
                }
                $this->events->dispatch($threadReward);
            }
        }
    }
}
