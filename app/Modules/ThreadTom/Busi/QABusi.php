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
use App\Models\User;
use App\Models\Order;
use App\Modules\ThreadTom\TomBaseBusi;
use App\Models\Question;
/*
 * obsolete busi class
 * */
class QABusi extends TomBaseBusi
{

    private $qaExpiredTime = 7;

    public function create()
    {
        $input = $this->verification();

        if ($input['beUserId'] == $this->user['id']) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, ResponseCode::$codeMap[ResponseCode::INVALID_PARAMETER]);
        }

        $inUser = User::query('id')->where('id',$input['beUserId'])->count();
        if (empty($inUser)){
            $this->outPut(ResponseCode::NOT_FOUND_USER, ResponseCode::$codeMap[ResponseCode::NOT_FOUND_USER]);
        }

        $order = Order::query()
            ->where('order_sn',$input['orderId'])
            ->first(['id','thread_id','user_id','status','amount','expired_at']);

        if (!empty($order['thread_id']) ||
            $order['user_id'] != $this->user['id'] ||
            $order['status'] != Order::ORDER_STATUS_PAID ||
            strtotime($order['expired_at']) < time()||
            $order['amount'] != $input['price']) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, ResponseCode::$codeMap[ResponseCode::INVALID_PARAMETER]);
        }

        $order->thread_id = $this->threadId;
        $order->save();

        if (empty($order['thread_id'])) {
            $this->outPut(ResponseCode::NOT_FOUND_USER, ResponseCode::$codeMap[ResponseCode::NOT_FOUND_USER]);
        }

        $question = new Question;
        $question->thread_id = $this->threadId;
        $question->user_id = $this->user['id'];
        $question->be_user_id = $input['beUserId'];
        $question->price = $input['price'];
        $question->is_onlooker = $input['isOnlooker'];
        $question->is_answer = Question::TYPE_OF_UNANSWERED;
        $question->expired_at = date('Y-m-d H:i:s',strtotime("+{$this->qaExpiredTime} day"));
        $question->save();

        $question->isSelect = false;

        return $this->jsonReturn($question);
    }

    public function select()
    {
        if (isset($this->body['isSelect'])) {
            return $this->jsonReturn($this->body);
        }

        $question = Question::query()
            ->where('id',$this->body['id'])
            ->first(['content','ip','port','onlooker_price','onlooker_number','is_answer','is_approved']);
        $this->body['content'] = $question['content'];
        $this->body['ip'] = $question['ip'];
        $this->body['port'] = $question['port'];
        $this->body['onlooker_price'] = $question['onlooker_price'];
        $this->body['onlooker_number'] = $question['onlooker_number'];
        $this->body['is_answer'] = $question['is_answer'];
        $this->body['is_approved'] = $question['is_approved'];

        return $this->jsonReturn($this->body);
    }

    public function verification()
    {
        $input = [
            'beUserId' => $this->getParams('beUserId'),
            'isOnlooker' => $this->getParams('isOnlooker'),
            'orderId' => $this->getParams('orderId'),
            'price' => $this->getParams('price'),
            'type' => $this->getParams('type')
        ];
        $rules = [
            'beUserId' => 'required|int',
            'isOnlooker' => 'required|boolean',
            'orderId' => 'required|numeric',
            'price' => 'required|numeric|min:0.01',
            'type' => 'required|integer|in:0,1'
        ];
        $this->dzqValidate($input, $rules);

        return $input;
    }
}
