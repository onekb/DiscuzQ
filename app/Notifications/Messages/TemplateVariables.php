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

namespace App\Notifications\Messages;

trait TemplateVariables
{
    protected $templateVariables = [
        1  => '系统新用户注册通知',        // 新用户注册通知
        2  => '系统注册审核通过通知',      // 注册审核通过通知
        3  => '系统注册审核不通过通知',    // 注册审核不通过通知
        4  => '系统内容审核不通过通知',    // 内容审核不通过通知
        5  => '系统内容审核通过通知',      // 内容审核通过通知
        6  => '系统内容删除通知',         // 内容删除通知
        7  => '系统内容精华通知',         // 内容精华通知
        8  => '系统内容置顶通知',         // 内容置顶通知
        9  => '系统内容修改通知',         // 内容修改通知
        10 => '系统用户禁用通知',         // 用户禁用通知
        11 => '系统用户解除禁用通知',      // 用户解除禁用通知
        12 => '系统用户角色调整通知',      // 用户角色调整通知
        13 => '微信新用户注册通知',        // 新用户注册通知
        14 => '微信用户状态通知',         // 注册审核通过通知
        15 => '微信用户状态通知',         // 注册审核不通过通知
        16 => '微信内容状态通知',         // 内容审核通过通知
        17 => '微信内容状态通知',         // 内容审核不通过通知
        18 => '微信内容状态通知',         // 内容删除通知
        19 => '微信内容状态通知',         // 内容精华通知
        20 => '微信内容状态通知',         // 内容置顶通知
        21 => '微信内容状态通知',         // 内容修改通知
        22 => '微信用户状态通知',         // 用户禁用通知
        23 => '微信用户状态通知',         // 用户解除禁用通知
        24 => '微信用户角色调整通知',      // 用户角色调整通知
        25 => '',                       // 内容回复通知
        26 => '',                       // 内容点赞通知
        27 => '',                       // 内容支付通知
        28 => '',                       // 内容@通知
        29 => '微信内容回复通知',         // 内容回复通知
        30 => '微信内容点赞通知',         // 内容点赞通知
        31 => '微信内容支付通知',         // 内容支付通知
        32 => '微信内容@通知',            // 内容@通知
        33 => '',                       // 提现通知
        34 => '',                       // 提现失败通知
        35 => '微信提现通知',             // 提现通知
        36 => '微信提现通知',             // 提现失败通知
        37 => '',                       // 分成收入通知
        38 => '微信分成收入通知',         // 分成收入通知
        39 => '',                       // 问答提问通知
        40 => '微信问答提问或过期通知',    // 问答提问通知
        41 => '',                       // 问答回答通知
        42 => '微信问答回答通知',         // 问答回答通知
        43 => '',                       // 过期通知
        44 => '微信问答提问或过期通知',    // 过期通知
        45 => '得到红包通知',            // 得到红包通知
        46 => '得到红包通知',            // 得到红包通知
        47 => '悬赏问答通知',            // 悬赏问答通知
        48 => '悬赏问答通知',            // 悬赏问答通知
        49 => '悬赏过期通知',            // 悬赏过期通知
        50 => '悬赏过期通知',            // 悬赏过期通知
    ];

    /**
     * 统一 type_name 名称，使其作为唯一标识
     *
     * @var array
     */
    protected $configTypeName = [
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

    /**
     * 初始化小程序路由
     *
     * @var string[]
     */
    protected $initPagePath = [
        // 注册相关内容 -> 跳转到首页
        13 => '/pages/home/index', // 首页
        14 => '/pages/home/index',
        15 => '/pages/home/index',
        16 => '/topic/index?id={$thread_id}', // 内容审核通过通知
        17 => '/topic/index?id={$thread_id}', // 内容审核不通过通知
        18 => '/pages/home/index', // 内容删除 跳首页
        19 => '/topic/index?id={$thread_id}', // 精华 -> 帖子详情页
        20 => '/topic/index?id={$thread_id}', // 置顶 -> 帖子详情页
        21 => '/topic/index?id={$thread_id}', // 修改 -> 帖子详情页
        22 => '/pages/notice/notice?title=系统通知&type=system', // 用户禁用 -> "系统通知" 页
        23 => '/pages/notice/notice?title=系统通知&type=system', // 用户解除 -> "系统通知" 页
        24 => '/pages/notice/notice?title=系统通知&type=system', // 用户角色 -> "系统通知" 页
        29 => '/pages/notice/notice?title=回复我的&type=replied', // 内容回复通知 -> "回复我的" 通知页
        30 => '/pages/notice/notice?title=点赞我的&type=liked', // 内容点赞通知 -> "点赞我的" 通知页
        31 => '/pages/notice/notice?title=财务通知&type=rewarded,withdrawal', // "财务通知" 页
        32 => '/topic/index?id={$thread_id}', // @通知 跳详情页
        // 提现通知、提现失败通知
        35 => '/pages/my/withdrawalslist', // 个人中心-我的钱包-提现记录页
        36 => '/pages/my/withdrawalslist',
        // 分成收入通知
        38 => '/pages/my/walletlist',  // 个人中心-我的钱包-钱包明细页
        // 问答通知都跳主题详情页
        40 => '/topic/index?id={$thread_id}',
        42 => '/topic/index?id={$thread_id}',
        44 => '/topic/index?id={$thread_id}',
        46 => '/topic/index?id={$thread_id}',
        48 => '/topic/index?id={$thread_id}',
        50 => '/topic/index?id={$thread_id}',
    ];
}
