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

namespace App\Notifications\Messages;

trait TemplateVariables
{
    /**
     * 统一 type_name 名称，使其作为同类通知的唯一标识
     * TODO 2021/1/20 用于老版本统一名称使用
     *
     * @var array
     */
    protected $originConfigTypeName = [
        1  => '新用户注册通知', 13 => '新用户注册通知',
        2  => '注册审核通过通知', 14 => '注册审核通过通知',
        3  => '注册审核不通过通知', 15 => '注册审核不通过通知',
        4  => '内容审核不通过通知', 17 => '内容审核不通过通知',
        5  => '内容审核通过通知', 16 => '内容审核通过通知',
        6  => '内容删除通知', 18 => '内容删除通知',
        7  => '内容精华通知', 19 => '内容精华通知',
        8  => '内容置顶通知', 20 => '内容置顶通知',
        9  => '内容修改通知', 21 => '内容修改通知',
        10 => '用户禁用通知', 22 => '用户禁用通知',
        11 => '用户解除禁用通知', 23 => '用户解除禁用通知',
        12 => '用户角色调整通知', 24 => '用户角色调整通知',
        25 => '内容回复通知', 29 => '内容回复通知',
        26 => '内容点赞通知', 30 => '内容点赞通知',
        27 => '内容支付通知', 31 => '内容支付通知',
        28 => '内容@通知', 32 => '内容@通知',
        33 => '提现通知', 35 => '提现通知',
        34 => '提现失败通知', 36 => '提现失败通知',
        37 => '分成收入通知', 38 => '分成收入通知',
        39 => '问答提问通知', 40 => '问答提问通知',
        41 => '问答回答通知', 42 => '问答回答通知',
        43 => '问答过期通知', 44 => '问答过期通知',
        45 => '得到红包通知', 46 => '得到红包通知',
        47 => '悬赏问答通知', 48 => '悬赏问答通知',
        49 => '悬赏过期通知', 50 => '悬赏过期通知',
    ];

    protected $templateVariables = [
        'system.registered.passed'     => '系统新用户注册通知',              // 新用户注册通知
        'system.registered.approved'   => '系统注册审核通过通知',             // 注册审核通过通知
        'system.registered.unapproved' => '系统注册审核不通过通知',            // 注册审核不通过通知
        'system.post.unapproved'       => '系统内容审核不通过通知',            // 内容审核不通过通知
        'system.post.approved'         => '系统内容审核通过通知',             // 内容审核通过通知
        'system.post.deleted'          => '系统内容删除通知',               // 内容删除通知
        'system.post.essence'          => '系统内容精华通知',               // 内容精华通知
        'system.post.sticky'           => '系统内容置顶通知',               // 内容置顶通知
        'system.post.update'           => '系统内容修改通知',               // 内容修改通知
        'system.user.disable'          => '系统用户禁用通知',               // 用户禁用通知
        'system.user.normal'           => '系统用户解除禁用通知',             // 用户解除禁用通知
        'system.user.group'            => '系统用户角色调整通知',             // 用户角色调整通知
        'system.post.replied'          => '',                       // 内容回复通知
        'system.post.liked'            => '',                       // 内容点赞通知
        'system.post.paid'             => '',                       // 内容支付通知
        'system.post.reminded'         => '',                       // 内容@通知
        'system.withdraw.noticed'      => '',                       // 提现通知
        'system.withdraw.withdraw'     => '',                       // 提现失败通知
        'system.divide.income'         => '',                       // 分成收入通知
        'system.question.asked'        => '',                       // 问答提问通知
        'system.question.answered'     => '',                       // 问答回答通知
        'system.question.expired'      => '',                       // 过期通知
        'system.red_packet.gotten'     => '',                       //得到红包通知
        'system.question.rewarded'     => '',                       //悬赏问答通知
        'system.question.rewarded.expired' => '',                   //悬赏过期通知
        'registered.passed'            => '微信新用户注册通知',              // 新用户注册通知
        'registered.approved'          => '微信用户状态通知',               // 注册审核通过通知
        'registered.unapproved'        => '微信用户状态通知',               // 注册审核不通过通知
        'post.approved'                => '微信内容状态通知',               // 内容审核通过通知
        'post.unapproved'              => '微信内容状态通知',               // 内容审核不通过通知
        'post.deleted'                 => '微信内容状态通知',               // 内容删除通知
        'post.essence'                 => '微信内容状态通知',               // 内容精华通知
        'post.sticky'                  => '微信内容状态通知',               // 内容置顶通知
        'post.update'                  => '微信内容状态通知',               // 内容修改通知
        'user.disable'                 => '微信用户状态通知',               // 用户禁用通知
        'user.normal'                  => '微信用户状态通知',               // 用户解除禁用通知
        'user.group'                   => '微信用户角色调整通知',             // 用户角色调整通知
        'post.replied'                 => '微信内容回复通知',               // 内容回复通知
        'post.liked'                   => '微信内容点赞通知',               // 内容点赞通知
        'post.paid'                    => '微信内容支付通知',               // 内容支付通知
        'post.reminded'                => '微信内容@通知',                // 内容@通知
        'withdraw.noticed'             => '微信提现通知',                 // 提现通知
        'withdraw.withdraw'            => '微信提现通知',                 // 提现失败通知
        'divide.income'                => '微信分成收入通知',               // 分成收入通知
        'question.asked'               => '微信问答提问或过期通知',            // 问答提问通知
        'question.answered'            => '微信问答回答通知',               // 问答回答通知
        'question.expired'             => '微信问答提问或过期通知',            // 过期通知
        'red_packet.gotten'            => '得到红包通知',                  //得到红包通知
        'question.rewarded'            => '悬赏问答通知',                  //悬赏问答通知
        'question.rewarded.expired'    => '悬赏过期通知',                  //悬赏过期通知
        'system.abnormal.order.refund' => '异常订单退款通知'              //异常订单退款通知
    ];

    protected $configTypeName = [
        'system.registered.passed'     => '新用户注册通知', 'wechat.registered.passed' => '新用户注册通知',
        'system.registered.approved'   => '注册审核通过通知', 'wechat.registered.approved' => '注册审核通过通知',
        'system.registered.unapproved' => '注册审核不通过通知', 'wechat.registered.unapproved' => '注册审核不通过通知',
        'system.post.unapproved'       => '内容审核不通过通知', 'wechat.post.unapproved' => '内容审核不通过通知',
        'system.post.approved'         => '内容审核通过通知', 'wechat.post.approved' => '内容审核通过通知',
        'system.post.deleted'          => '内容删除通知', 'wechat.post.deleted' => '内容删除通知',
        'system.post.essence'          => '内容精华通知', 'wechat.post.essence' => '内容精华通知',
        'system.post.sticky'           => '内容置顶通知', 'wechat.post.sticky' => '内容置顶通知',
        'system.post.update'           => '内容修改通知', 'wechat.post.update' => '内容修改通知',
        'system.user.disable'          => '用户禁用通知', 'wechat.user.disable' => '用户禁用通知',
        'system.user.normal'           => '用户解除禁用通知', 'wechat.user.normal' => '用户解除禁用通知',
        'system.user.group'            => '用户角色调整通知', 'wechat.user.group' => '用户角色调整通知',
        'system.post.replied'          => '内容回复通知', 'wechat.post.replied' => '内容回复通知',
        'system.post.liked'            => '内容点赞通知', 'wechat.post.liked' => '内容点赞通知',
        'system.post.paid'             => '内容支付通知', 'wechat.post.paid' => '内容支付通知',
        'system.post.reminded'         => '内容@通知', 'wechat.post.reminded' => '内容@通知',
        'system.withdraw.noticed'      => '提现通知', 'wechat.withdraw.noticed' => '提现通知',
        'system.withdraw.withdraw'     => '提现失败通知', 'wechat.withdraw.withdraw' => '提现失败通知',
        'system.divide.income'         => '分成收入通知', 'wechat.divide.income' => '分成收入通知',
        'system.question.asked'        => '问答提问通知', 'wechat.question.asked' => '问答提问通知',
        'system.question.answered'     => '问答回答通知', 'wechat.question.answered' => '问答回答通知',
        'system.question.expired'      => '问答过期通知', 'wechat.question.expired' => '问答过期通知',
        'system.abnormal.order.refund' => '异常订单退款通知'
    ];

    /**
     * 初始化小程序路由
     *
     * @var string[]
     */
    protected $initPagePath = [
        // 注册相关内容 -> 跳转到首页
        'registered.passed'         => '/pages/home/index', // 首页
        'registered.approved'       => '/pages/home/index',
        'registered.unapproved'     => '/pages/home/index',
        'post.approved'             => '/topic/index?id={$thread_id}',                             // 内容审核通过通知
        'post.unapproved'           => '/topic/index?id={$thread_id}',                             // 内容审核不通过通知
        'post.deleted'              => '/pages/home/index',                                        // 内容删除 跳首页
        'post.essence'              => '/topic/index?id={$thread_id}',                             // 精华 -> 帖子详情页
        'post.sticky'               => '/topic/index?id={$thread_id}',                             // 置顶 -> 帖子详情页
        'post.update'               => '/topic/index?id={$thread_id}',                             // 修改 -> 帖子详情页
        'user.disable'              => '/pages/notice/notice?title=系统通知&type=system',              // 用户禁用 -> "系统通知" 页
        'user.normal'               => '/pages/notice/notice?title=系统通知&type=system',              // 用户解除 -> "系统通知" 页
        'user.group'                => '/pages/notice/notice?title=系统通知&type=system',              // 用户角色 -> "系统通知" 页
        'post.replied'              => '/pages/notice/notice?title=回复我的&type=replied',             // 内容回复通知 -> "回复我的" 通知页
        'post.liked'                => '/pages/notice/notice?title=点赞我的&type=liked',               // 内容点赞通知 -> "点赞我的" 通知页
        'post.paid'                 => '/pages/notice/notice?title=财务通知&type=rewarded,withdrawal', // "财务通知" 页
        'post.reminded'             => '/topic/index?id={$thread_id}',                             // @通知 跳详情页
        // 提现通知、提现失败通知
        'withdraw.noticed'          => '/pages/my/withdrawalslist',                                // 个人中心-我的钱包-提现记录页
        'withdraw.withdraw'         => '/pages/my/withdrawalslist',
        // 分成收入通知
        'divide.income'             => '/pages/my/walletlist',                                     // 个人中心-我的钱包-钱包明细页
        // 问答通知都跳主题详情页
        'question.asked'            => '/topic/index?id={$thread_id}',
        'question.answered'         => '/topic/index?id={$thread_id}',
        'question.expired'          => '/topic/index?id={$thread_id}',
        // 红包通知
        'red_packet.gotten'         => '/topic/index?id={$thread_id}',
        'question.rewarded'         => '/topic/index?id={$thread_id}',
        'question.rewarded.expired' => '/topic/index?id={$thread_id}',
    ];

    /**
     * 模板唯一字符串
     * 新通知起名规范：{ 频道名(channel) }.{ 模块名 }.{ 行为(过去式的英文单词) }
     * 注意 频道名 会在 @method comparisonUnique // 方法中拼接，成员属性中只放后两者
     *
     * @var string[]
     */
    protected $uniquelyNotice = [
        'registered.passed'         => '新用户注册通知',
        'registered.approved'       => '注册审核通过通知',
        'registered.unapproved'     => '注册审核不通过通知',
        'post.unapproved'           => '内容审核不通过通知',
        'post.approved'             => '内容审核通过通知',
        'post.deleted'              => '内容删除通知',
        'post.essence'              => '内容精华通知',
        'post.sticky'               => '内容置顶通知',
        'post.update'               => '内容修改通知',
        'user.disable'              => '用户禁用通知',
        'user.normal'               => '用户解除禁用通知',
        'user.group'                => '用户角色调整通知',
        'post.replied'              => '内容回复通知',
        'post.liked'                => '内容点赞通知',
        'post.paid'                 => '内容支付通知',
        'post.reminded'             => '内容@通知',
        'withdraw.noticed'          => '提现通知',
        'withdraw.withdraw'         => '提现失败通知',
        'divide.income'             => '分成收入通知',
        'question.asked'            => '问答提问通知',
        'question.answered'         => '问答回答通知',
        'question.expired'          => '问答过期通知',
        'red_packet.gotten'         => '得到红包通知',
        'question.rewarded'         => '悬赏问答通知',
        'question.rewarded.expired' => '悬赏过期通知',
        'abnormal.order.refund'     => '异常订单退款通知'
    ];

    /**
     * 获取小程序路由
     *
     * @param $noticeId
     * @return string
     */
    protected function getInitPagePath($noticeId)
    {
        // 判断前缀是什么
        $arr = explode('.', $noticeId);
        $prefix = array_shift($arr);

        if (in_array($prefix, ['wechat', 'miniprogram'])) {
            $noticeId = implode('.', $arr);
            return isset($this->initPagePath[$noticeId]) ? $this->initPagePath[$noticeId] : '';
        }

        return '';
    }

    /**
     * 获取变量渲染静态数据
     *
     * @param $noticeId
     * @return string
     */
    protected function getTemplateVariables($noticeId)
    {
        // 判断前缀是什么
        $arr = explode('.', $noticeId);
        $prefix = array_shift($arr);

        if ($prefix == 'system') {
            return $this->templateVariables[$noticeId];
        }

        // 除系统通知，其余通知变量均一样
        $noticeId = implode('.', $arr);
        return isset($this->templateVariables[$noticeId]) ? $this->templateVariables[$noticeId] : '';
    }

    /**
     * 拼接/获取 通知唯一标识
     *
     * @param string $typeName
     * @param int $type
     * @return string|null
     */
    protected function comparisonUnique(string $typeName, int $type)
    {
        // 通知类型: 0系统 1微信 2短信 3企业微信 4小程序通知
        switch ($type) {
            default:
            case 0:
                $prefix = 'system.';
                break;
            case 1:
                $prefix = 'wechat.';
                break;
            case 2:
                $prefix = 'sms.';
                break;
            case 4:
                $prefix = 'miniprogram.';
                break;
        }

        // 搜索定义的唯一标识名单
        $result = array_search($typeName, $this->uniquelyNotice);

        // 未搜索到时返回 null 值
        if (! $result) {
            return null;
        }

        return $prefix . $result;
    }
}
