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

namespace App\Models;

use App\Common\CacheKey;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $group_id
 * @property string $permission
 */
class Permission extends DzqModel
{
    const DEFAULT_PERMISSION = [
        'thread.favorite',              // 收藏
        'thread.likePosts',             // 点赞
        'userFollow.create',            // 关注
        'user.view',                    // 查看个人信息，目前仅用于前台显示权限
        'order.create',                 // 创建订单
        'trade.pay.order',              // 支付订单
        'cash.create',                  // 提现
    ];


    const THREAD_PERMISSION = [
        'switch.createThread' => '发布帖子',           //开启/允许发布帖子
        'thread.insertImage' => '插入图片',          //开启/允许插入图片
        'thread.insertVideo' => '插入视频',             //开启/允许发布视频
        'thread.insertAudio' => '插入语音',            //开启/允许发布语音
        'thread.insertAttachment' => '插入附件',             //开启/允许发布附件
        'thread.insertGoods' => '插入商品',           //开启/允许发布商品
        'thread.insertPay' => '插入付费',            //开启/允许发布付费
        'thread.insertReward' => '插入悬赏',          //开启/允许发布悬赏
        'thread.insertRedPacket' => '插入红包',         //开启/允许发布红包
        'thread.insertPosition' => '插入位置',         //开启/允许发布位置
        'thread.allowAnonymous' => '插入匿名贴',         //开启/允许发布匿名贴
        'thread.insertVote'     =>  '插入投票'
    ];


    /**
     * {@inheritdoc}
     */
    protected $table = 'group_permission';

    /**
     * {@inheritdoc}
     */
    protected $fillable = ['group_id', 'permission'];

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    /**
     * Define the relationship with the group that this permission is for.
     *
     * @return BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    private static function categoryPermissions($groupIds = [])
    {
        $permissions = Permission::query()->whereIn('group_id', $groupIds)->get()->toArray();
        $permissions = array_column($permissions, 'permission');
        return $permissions;
    }

    /**
     * @desc 获取用户组下的权限列表
     * @param $user
     * @return array|mixed|object
     */
    public static function getUserPermissions($user)
    {
        $groups = $user->groups->toArray();
        $groupIds = array_column($groups, 'id');
        $groupKey = md5(serialize($groupIds));
        $cache = app('cache');
        $key = CacheKey::GROUP_PERMISSIONS;
        if (app()->has($key)) {
            $permissions = app()->get($key);
        } else {
            $permissions = $cache->get($key);
        }
        if ($permissions) {
            if (isset($permissions[$groupKey])) {
                $permission = $permissions[$groupKey];
            } else {
                $permission = Permission::categoryPermissions($groupIds);
                $permissions[$groupKey] = $permission;
            }
        } else {
            $permission = Permission::categoryPermissions($groupIds);
            $permissions = [$groupKey => $permission];
        }
        $cache->put($key, $permissions, 5 * 60);
        app()->instance($key, $permissions);
        return $permission;
    }

    protected function clearCache()
    {
        DzqCache::delKey(CacheKey::GROUP_PERMISSIONS);
    }
}
