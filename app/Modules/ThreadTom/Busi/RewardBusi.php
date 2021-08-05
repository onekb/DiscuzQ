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

namespace App\Modules\ThreadTom\Busi;

use App\Common\ResponseCode;
use App\Common\Utils;
use App\Models\Thread;
use App\Modules\ThreadTom\TomBaseBusi;
use App\Models\Order;
use App\Models\OrderChildren;
use App\Models\ThreadReward;
use App\Models\ThreadTom;
use Carbon\Carbon;

class RewardBusi extends TomBaseBusi
{
    const NEED_PAY = true;

    public function create()
    {
        $input = $this->verification();
        // 如果有对应的已支付订单，则直接找出之前的  $threadReward  返回，不走后面的逻辑
        $res = self::orderPaidJudge($input);
        if($res['code'] != ResponseCode::SUCCESS){
            $this->outPut($res['code'], $res['msg']);
        }
        if(!empty($res['data'])){
            return $this->jsonReturn($res['data']);
        }


        if(strtotime($input['expiredAt']) < time()+24*60*60){
            $this->outPut(ResponseCode::INVALID_PARAMETER, '悬赏时间应超过当前时间24小时');
        }
        $thread = Thread::query()->find($this->threadId);
        if(empty($thread->is_draft)){       //已发布的帖子，不允许在增加悬赏tom
            $this->outPut(ResponseCode::INVALID_PARAMETER, '已发布的帖子不允许增加悬赏');
        }
        if (!empty($input['orderSn'])) {
            $order = Order::query()
                ->where('order_sn',$input['orderSn'])
                ->first(['id','thread_id','user_id','status','amount','expired_at','type']);
            if(empty($order)){
                $this->outPut(ResponseCode::INTERNAL_ERROR, '订单不存在');
            }

            if (
                ( $order->type == Order::ORDER_TYPE_QUESTION_REWARD && !empty($order['thread_id']) ) ||
                !in_array($order->type, [Order::ORDER_TYPE_QUESTION_REWARD, Order::ORDER_TYPE_MERGE]) ||
                $order['user_id'] != $this->user['id'] ||
                $order['status'] != Order::ORDER_STATUS_PENDING ||
                (!empty($order['expired_at']) && strtotime($order['expired_at']) < time())||
                ($order->type == Order::ORDER_TYPE_QUESTION_REWARD && Utils::compareMath($order->amount, $input['price']) )
            ) {
                $this->outPut(ResponseCode::RESOURCE_EXPIRED, '订单已过期或异常，请重新创建订单');
            }

            if ($order->type == Order::ORDER_TYPE_MERGE) {
                $orderChildrenInfo = OrderChildren::query()
                    ->where('order_sn', $input['orderSn'])
                    ->where('type', Order::ORDER_TYPE_QUESTION_REWARD)
                    ->first();
                if (empty($orderChildrenInfo) ||
                    Utils::compareMath($orderChildrenInfo->amount, $input['price']) ||
                    $orderChildrenInfo->status != Order::ORDER_STATUS_PENDING) {
                    $this->outPut(ResponseCode::RESOURCE_EXPIRED,'子订单异常');
                }
                $orderChildrenInfo->thread_id = $this->threadId;
                $orderChildrenInfo->save();
            }

            $order->thread_id = $this->threadId;
            $order->save();

            if (empty($order['thread_id'])) {
                $this->outPut(ResponseCode::INTERNAL_ERROR, '订单回填主题id出错');
            }
        }
        // 创建对应的红包 tom 时，需要同时配套创建 thread_reward 数据
        $threadReward = new ThreadReward;
        $threadReward->thread_id = $this->threadId;
        $threadReward->post_id = $this->postId;
        $threadReward->type = $input['type'];
        $threadReward->user_id = $this->user['id'];
        $threadReward->answer_id = 0; // 目前没有指定人问答
        $threadReward->money = round($input['price'], 2);
        $threadReward->remain_money = round($input['price'], 2);
        $threadReward->expired_at = date('Y-m-d H:i:s', strtotime($input['expiredAt']));
        $threadReward->save();

        $threadReward->content = $input['content'];

        return $this->jsonReturn($threadReward);
    }

    public function update()
    {
        $input = $this->verification();
        // 如果有对应的已支付订单，则直接找出之前的  $threadReward  返回，不走后面的逻辑
        $res = self::orderPaidJudge($input);
        if($res['code'] != ResponseCode::SUCCESS){
            $this->outPut($res['code'], $res['msg']);
        }
        if(!empty($res['data'])){
            return $this->jsonReturn($res['data']);
        }
        $threadReward = ThreadReward::query()->where(['thread_id' => $this->threadId, 'post_id' => $this->postId])->first();
        if(empty($threadReward)){
            $this->outPut(ResponseCode::INTERNAL_ERROR, '原悬赏帖数据不存在');
        }
        //先删除原订单，这里的删除暂定为：将原订单中的 thread_id 置 0，让原订单成为僵死订单
        $old_order = Order::query()->where('thread_id', $this->threadId)->first();
        if($old_order && $old_order->type == Order::ORDER_STATUS_PAID){       //如果订单已支付，则不允许做修改操作，直接返回之前的操作
            return $this->jsonReturn($threadReward);
        }
//        下面的判断暂时去掉，考虑允许在没有支付的情况下，允许用户删掉之前的红包，然后创建新的红包，直接保存到草稿，没有点发布，就不需要用户创建订单
//        if(empty($input['orderSn']) && !empty($old_order)){
//            $this->outPut(ResponseCode::INVALID_PARAMETER, '该贴已有订单，缺少 orderSn');
//        }

        //如果该帖具有老订单了，并且本次请求的orderSn 与老订单的 order_sn 相同的话，则取出老 $threadReward 返回就好了
        if($old_order && $old_order->order_sn && $old_order->order_sn == $input['orderSn'] ){
            return $this->jsonReturn($threadReward);
        }
        if(!empty($input['orderSn'])){
            $order = Order::query()->where('order_sn', $input['orderSn'])->first();
            if(empty($order)){
                $this->outPut(ResponseCode::INTERNAL_ERROR, 'orderSn不正确');
            }
        }
        //如果传过来的 orderSn 变更的话，就说明红包变了，那么就与原 order 脱离关系，关联新 order（这里允许 orderSn 为空）
        if($old_order &&  $old_order->order_sn != $input['orderSn']) {
            //规定时间内，含有红包的帖子不能频繁修改
            if ($old_order->created_at > Carbon::now()->subMinutes(self::RED_LIMIT_TIME)) {
                $this->outPut(ResponseCode::INTERNAL_ERROR, '系统处理中，请等待1分钟之后再发布......');
            }
            $old_order->thread_id = 0;
            $res = $old_order->save();
            if ($res === false) {
                $this->outPut(ResponseCode::INTERNAL_ERROR, '清除原订单帖子id失败');
            }
            // 将原 orderChildrenInfo 的 thread_id 置 0
            if ($old_order->type == Order::ORDER_TYPE_MERGE) {
                $orderChildrenInfo = OrderChildren::query()
                    ->where('order_sn', $input['orderSn'])
                    ->where('type', Order::ORDER_TYPE_QUESTION_REWARD)
                    ->first();
                if (empty($orderChildrenInfo) || $orderChildrenInfo->status == Order::ORDER_STATUS_PAID) {
                    $this->outPut(ResponseCode::RESOURCE_EXPIRED, '子订单异常');
                }
                $orderChildrenInfo->thread_id = 0;
                $res = $orderChildrenInfo->save();
                if ($res === false) {
                    $this->outPut(ResponseCode::INTERNAL_ERROR, '清除原子订单帖子id失败');
                }
            }
        }
        // 将原 threadReward 中 thread_id 、post_id 置 0
        $threadReward->thread_id = 0;
        $threadReward->post_id = 0;
        $res = $threadReward->save();
        if($res === false){
            $this->outPut(ResponseCode::INTERNAL_ERROR, '修改原悬赏帖数据出错');
        }
        return self::create();
    }

    public function select()
    {
        $redPacket = ThreadReward::query()->where('id',$this->body['id'])->first(['remain_money']);
        $this->body['remain_money'] = $redPacket['remain_money'];

        return $this->jsonReturn($this->camelData($this->body));
    }

    public function verification(){
        $input = [
            'orderSn' => $this->getParams('orderSn'),
            'price' => $this->getParams('price'),
            'type' => $this->getParams('type'),
            'expiredAt' => $this->getParams('expiredAt'),
            'content' => $this->getParams('content'),
//            'draft' => $this->getParams('draft')
        ];
        $rules = [
            'price' => 'required|numeric|min:0.1|max:1000000',
            'type' => 'required|integer|in:0,1',
            'expiredAt' => 'required|date',
            'content' => 'max:1000',
        ];

//        $input['draft'] != Thread::IS_DRAFT ? $rules['orderSn'] = 'required|numeric' : '';

        $this->dzqValidate($input, $rules);

        return $input;
    }

    public function orderPaidJudge($input){
        if($order = self::getRedOrderInfo($this->threadId)){
            if($order->status == Order::ORDER_STATUS_PAID){
                $threadReward = ThreadReward::query()->where(['thread_id' => $this->threadId, 'post_id' => $this->postId])->first();
                if($threadReward){
                    if(
                        $threadReward->type != $input['type'] ||
                        $threadReward->user_id != $this->user['id'] ||
                        Utils::compareMath($threadReward->money, $input['price'])
                    ){
                        return  [
                            'code'  =>  ResponseCode::INVALID_PARAMETER,
                            'msg'   =>  '已发布的悬赏不可修改'
                        ];
                    }
                    return  [
                        'code'  =>  ResponseCode::SUCCESS,
                        'msg'   =>  '',
                        'data'  =>  $threadReward
                    ];
                }else{
                    return  [
                        'code'  =>  ResponseCode::INTERNAL_ERROR,
                        'msg'   =>  '原悬赏帖数据有误，缺少悬赏数据'
                    ];
                }
            }
        }
        return  [
            'code'  =>  ResponseCode::SUCCESS,
            'msg'   =>  '',
            'data'  =>  ''
        ];
    }
}
