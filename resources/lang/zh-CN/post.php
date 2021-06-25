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

/**
 * post_[type]_*   类型错误
 * not_not_*       拒绝/不允许
 */
return [
    // 帖子默认内容
    'default_content' => [
        'video' => '分享视频',
        'image' => '分享图片',
        'audio' => '分享语音',
        'goods' => '分享商品',
    ],

    'post_not_found' => '未查询到该条回复',
    'post_not_comment' => '不能回复，回复回帖的内容', // 点评

    // goods商品解析
    'post_goods_not_found_address' => '未匹配到地址格式',
    'post_goods_does_not_resolve' => '域名地址不在解析范围内',
    'post_goods_not_found_regex' => '未匹配到地址信息',
    'post_goods_fail_url' => '匹配到解析地址错误',
    'post_goods_not_found_enum' => '暂不支持解析该地址内容',
    'post_goods_http_client_fail' => '请求地址失败',
    'post_goods_frequently_fail' => '因频繁解析，请在1分钟后重新尝试解析',

    'post_question_missing_parameter' => '问答缺失参数',
    'post_question_edit_fail_answered' => '问答帖已回答后不允许修改',
    'post_question_payment_amount_fail' => '问答支付金额异常',
    'post_question_ask_yourself_fail' => '不能向自己提问',
    'post_question_ask_be_user_permission_denied' => '被提问用户没有权限回答',
    'post_question_order_pay_status_fail' => '问答支付状态异常',

    'post_thread_missing_parameter' => '帖子缺失参数',

    'thread_reward_answer_id_is_null' => '问答帖的指定人不可为空',
    'thread_reward_money_type_fail' => '悬赏金额必须为整数',
    'thread_reward_money_min_limit_fail' => '悬赏金额应大于0.1元',
    'thread_reward_money_max_limit_fail' => '悬赏金额不得超过10000元',
    'thread_reward_expired_time_is_null' => '悬赏结束时间不可为空',
    'thread_reward_expired_time_limit_fail' => '悬赏结束时间应大于一天',

    'post_reward_does_not_have_type' => '未获取到问答帖类型：向所有人提问还是向指定人提问？',
    'post_reward_does_not_have_thread_id' => '未获取到当前的帖子ID',
    'post_reward_does_not_have_post_id' => '未获取到当前的评论ID',
    'post_reward_post_user_id_limit' => '不可采纳自己的评论',
    'post_reward_the_rewards_limit_fail' => '悬赏金额应大于0元',
    'post_reward_detail_not_found' => '未获取到悬赏帖相关信息',
    'post_reward_user_limit' => '您不是帖子主人，不可进行采纳回答的操作',
    'post_reward_is_over' => '悬赏帖已到期',
    'post_reward_the_rewards_is_use_up' => '悬赏金已发放完毕',
    'post_reward_not_sufficient_funds' => '悬赏金余额不足',
    'post_reward_thread_detail_not_found' => '该帖正在审核或者已被删除',
    'post_reward_post_detail_not_found' => '该评论不是一级评论或者已被删除',
    'post_reward_type_error' => '悬赏金额类型错误，必须是数字！',
    'post_reward_order_error' => '悬赏帖订单信息错误，无法进行采纳！',
    'post_reward_user_wallet_error' => '您的冻结金额不足，悬赏采纳失败！',
    'post_reward_not_equal_to_order_price' => '悬赏帖总悬赏金额与订单实付金额不匹配，无法进行采纳',

    'thread_id_not_null' => '帖子id不可为空',
    'audio_video_not_null'  => '音视频不可为空',
    'audio_video_is_being_transcoded' => '音视频正在转码中，请稍后重试。',
    'audio_video_transcoding_failed'  => '音视频转码失败，请重新上传。',

    'thread_content_checktext_fail' => '您的主题内容或含敏感词，主题发送失败'
];
