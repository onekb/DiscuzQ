<?php

return [
    'operate_type_error'     => '操作类型不存在',
    'wallet_status_error'     => '钱包状态不存在',
    'available_amount_error' => '钱包可用金额不足',
    'freeze_amount_error' => '钱包冻结金额不足',
    'status_cash_freeze'     => '钱包已冻结提现',

    'question_freeze_desc'   => '问答冻结',
    'question_return_thaw_desc'  => '问答返还解冻',
    'income_thread_reward_return_desc'  => '悬赏帖过期-返还剩余悬赏金额',
    'income_thread_reward_divide_desc'  => '悬赏帖过期-平分剩余悬赏金额',
    'income_thread_reward_distribution_desc'  => '悬赏帖过期-按点赞数比例分配剩余悬赏金额',

    'cash_freeze_desc'       => '提现冻结',
    'cash_sum_limit'       => '超出每日提现金额限制',
    'cash_interval_time'       => '提现处于限制间隔天数内',
    'cash_success'           => '提现成功，扣除',
    'cash_failure'           => '提现失败，退回',
    'cash_review_failure'           => '审核不通过，退回',
    'operate_forbidden' => '非法操作',
    'pem_notexist' => '证书不存在，请先上传证书',

    // user_wallet_log
    'detail_type_does_not_exist' => '钱包明细类型',

    // 分割线

    'income_register'           => '注册收入',
    'income_scale_register'     => '注册分成收入',
    'income_artificial'         => '人工收入',
    'income_reward'             => '打赏主题收入',
    'income_scale_reward'       => '分成打赏主题收入',
    'income_thread'             => '付费主题收入',
    'income_scale_thread'       => '分成付费主题收入',
    'income_question_reward'    => '问答答题收入',
    'income_onlooker_reward'    => '问答围观收入',
    'income_attachment'         => '付费附件收入',
    'income_scale_attachment'   => '分成付费附件收入',
    'income_thread_reward'      => '悬赏问答答题收入',

    'expend_register'           => '注册支出',
    'expend_renew'              => '站点续费支出',
    'expend_reward'             => '打赏主题支出',
    'expend_thread'             => '付费主题支出',
    'expend_group'              => '加入用户组支出',
    'expend_artificial'         => '人工支出',
    'expend_question'           => '问答提问支出',
    'expend_onlooker'           => '问答围观支出',
    'expend_attachment'         => '付费附件支出',
    'freeze_text'               => '文字帖红包冻结',
    'expend_text'               => '文字帖红包支出',
    'income_text'               => '文字帖红包收入',
    'return_text'               => '文字帖红包冻结返还',
    'abnormal_return_text'      => '文字帖订单异常返现',
    'freeze_long'               => '长文帖红包冻结',
    'expend_long'               => '长文帖红包支出',
    'income_long'               => '长字帖红包收入',
    'return_long'               => '长文帖红包冻结返还',
    'abnormal_return_long'      => '长文帖订单异常返现',
    'abnormal_return_question'  => '问答帖订单异常返现',

    'cash_operate_desc'         => '提现',


    'unbind_wechat'         => '请绑定微信后再进行操作',

    'cash_type_error'       => '提现方式不存在',
    'cash_mch_invalid'      => '未开启微信企业付款到零钱功能',

    'abnormal_order_refund' => '异常订单退款',

    'redpacket_freeze' => '红包冻结',
    'redpacket_expend' => '红包支出',
    'redpacket_income' => '红包收入',
    'redpacket_refund' => '红包过期退款',
    'redpacket_order_abnormal_refund' => '红包订单异常退款',
    'question_reward_freeze' => '悬赏冻结',
    'question_reward_income' => '悬赏收入',
    'question_reward_refund' => '悬赏过期退款',
    'question_order_abnormal_refund' => '悬赏订单异常退款',
    'question_reward_expend' => '悬赏采纳支出',
    'question_reward_freeze_return' => '悬赏冻结返回',
    'merge_freeze' => '合并订单冻结',
    'merge_refund' => '合并订单退款',
    'merge_order_abnormal_refund' => '合并订单异常退款'
];
