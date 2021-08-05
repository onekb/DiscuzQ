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

use App\Common\PermissionKey;
use App\Models\Permission;
use App\Models\Thread;
use Illuminate\Database\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * 默认用户组 1 为超级管理员有以下的所有权限
     *
     * @var array
     */
    protected $permissions = [
        //通用权限：
        PermissionKey::CASH_CREATE                  => [10],    // 申请提现
        PermissionKey::ORDER_CREATE                 => [10,6],  // 创建订单
        PermissionKey::THREAD_FAVORITE              => [10],    // 帖子收藏
        PermissionKey::THREAD_LIKE_POSTS            => [10],    // 帖子点赞
        PermissionKey::TRADE_PAY_ORDER              => [10,6],  // 订单支付
        PermissionKey::USER_VIEW                    => [10],    // 查看某个用户信息权限
        PermissionKey::USER_FOLLOW_CREATE           => [10],    // 关注/取关用户

        // 内容发布权限
        'switch.'.PermissionKey::CREATE_THREAD      => [10],    // 发布帖子-左侧勾选按钮
        PermissionKey::CREATE_THREAD                => [10],    // 发布帖子-生效范围-全局
        PermissionKey::THREAD_INSERT_IMAGE          => [10,6],  // 插入图片
//        PermissionKey::THREAD_INSERT_VIDEO          => [],    // 插入视频
//        PermissionKey::THREAD_INSERT_AUDIO          => [],    // 插入语音
        PermissionKey::THREAD_INSERT_ATTACHMENT     => [10,6],  // 插入附件
        PermissionKey::THREAD_INSERT_GOODS          => [10],    // 插入商品
        PermissionKey::THREAD_INSERT_PAY            => [10],    // 插入付费
        PermissionKey::THREAD_INSERT_REWARD         => [10],    // 插入悬赏
        PermissionKey::THREAD_INSERT_RED_PACKET     => [10],    // 插入红包
        PermissionKey::THREAD_INSERT_POSITION       => [10],    // 插入位置
        PermissionKey::THREAD_ALLOW_ANONYMOUS       => [10],    // 允许匿名
        PermissionKey::DIALOG_CREATE                => [10],    // 发布私信
        'switch.'.PermissionKey::THREAD_REPLY       => [10],    // 回复主题-左侧勾选按钮
        PermissionKey::THREAD_REPLY                 => [10],    // 回复主题-生效范围-全局

        //查看权限
        'switch.'.PermissionKey::VIEW_THREADS       => [10,7],  // 查看主题列表-左侧勾选按钮
        PermissionKey::VIEW_THREADS                 => [10,7],  // 查看主题列表-生效范围-全局
        'switch.'.PermissionKey::THREAD_VIEW_POSTS  => [10,7],  // 查看主题详情-左侧勾选按钮
        PermissionKey::THREAD_VIEW_POSTS            => [10,7],  // 查看主题详情-生效范围-全局

        //管理权限
        'switch.'.PermissionKey::THREAD_EDIT_OWN    => [10],    // 编辑自己的主题-左侧勾选按钮
        PermissionKey::THREAD_EDIT_OWN              => [10],    // 编辑自己的主题-生效范围-全局
        'switch.'.PermissionKey::THREAD_HIDE_OWN    => [10],    // 删除自己的主题或回复-左侧勾选按钮
        PermissionKey::THREAD_HIDE_OWN              => [10],    // 删除自己的主题或回复-生效范围-全局

    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = collect($this->permissions)->map(function ($value, $key) {
            return collect($value)->map(function ($value) use ($key) {
                return [
                    'group_id' => $value,
                    'permission' => $key
                ];
            });
        })->reduce(function ($value, $item) {
            return $item->merge($value);
        });

        Permission::query()->truncate();
        Permission::query()->insert($data->toArray());
    }
}
