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

namespace App\Console\Commands\Upgrades;

use App\Common\PermissionKey;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Thread;
use App\Models\User;
use Discuz\Console\AbstractCommand;
use Illuminate\Database\ConnectionInterface;

class UpgradeOldPermissionDataCommand extends AbstractCommand
{
    protected $signature = 'upgrade:upgradeOldPermissionData';

    protected $description = '将老站点的权限数据升级为3.0版本';

    public $debugInfo = false;// 用于调试显示详细的处理信息

    //1.直接删除的老权限
    public $deleteOldPermission = [
        'canBeAsked'                        , // 允许被提问
        'canBeOnlooker'                     , // 设置围观

//        'switch.createThread'               , // 发布主题-左侧勾选按钮
//        'createThread'                      , // 发布主题-生效范围-全局

        'switch.thread.canBeReward'         , // 允许被打赏
        'thread.canBeReward'                , // 允许被打赏

        'switch.thread.freeViewPosts.2'     , // 免费查看付费视频
        'thread.freeViewPosts.2'            , // 免费查看付费视频

        'switch.thread.freeViewPosts.3'     , // 免费查看付费图片
        'thread.freeViewPosts.3'            , // 免费查看付费图片

        'switch.thread.freeViewPosts.4'     , // 免费查看付费语音
        'thread.freeViewPosts.4'            , // 免费查看付费语音

        'switch.thread.freeViewPosts.5'     , // 免费查看付费问答
        'thread.freeViewPosts.5'            , // 免费查看付费问答

        'createInvite'                      , // 邀请加入
        'user.edit.group'                   , // 编辑用户组
        'user.edit.status'                  , // 编辑用户状态

        'switch.thread.editPosts'           , // 编辑回复
        'thread.editPosts'                  , // 编辑回复
    ];

    //2.需删除的分类权限
    public $deleteOldCategoryPermission = [
        'categorycategoryId.createThread'            , // 发布主题
        'categorycategoryId.thread.freeViewPosts.2'  , // 免费查看付费视频
        'categorycategoryId.thread.freeViewPosts.3'  , // 免费查看付费图片
        'categorycategoryId.thread.freeViewPosts.4'  , // 免费查看付费语音
        'categorycategoryId.thread.freeViewPosts.5'  , // 免费查看付费问答
        'categorycategoryId.thread.editPosts'        , // 编辑回复
    ];

    //3.将key 重命名为 value的权限
    public $renameOldPermission = [
        'createThread.' . Thread::TYPE_OF_TEXT                      => PermissionKey::CREATE_THREAD, // 发布文字
        'createThread.' . Thread::TYPE_OF_LONG                      => PermissionKey::CREATE_THREAD, // 发布帖子
        'createThread.' . Thread::TYPE_OF_TEXT . '.position'        => PermissionKey::THREAD_INSERT_POSITION, // 发布文字位置
        'createThread.' . Thread::TYPE_OF_LONG . '.position'        => PermissionKey::THREAD_INSERT_POSITION, // 发布长文位置
        'createThread.' . Thread::TYPE_OF_VIDEO . '.position'       => PermissionKey::THREAD_INSERT_POSITION, // 发布视频位置
        'createThread.' . Thread::TYPE_OF_IMAGE . '.position'       => PermissionKey::THREAD_INSERT_POSITION, // 发布图片位置
        'createThread.' . Thread::TYPE_OF_AUDIO . '.position'       => PermissionKey::THREAD_INSERT_POSITION, // 发布语音位置
        'createThread.' . Thread::TYPE_OF_QUESTION . '.position'    => PermissionKey::THREAD_INSERT_POSITION, // 发布问答位置
        'createThread.' . Thread::TYPE_OF_GOODS . '.position'       => PermissionKey::THREAD_INSERT_POSITION, // 发布商品位置

        'createThread.' . Thread::TYPE_OF_TEXT . '.redPacket'       => PermissionKey::THREAD_INSERT_RED_PACKET, // 发文字帖红包
        'createThread.' . Thread::TYPE_OF_LONG . '.redPacket'       => PermissionKey::THREAD_INSERT_RED_PACKET, // 发长文帖红包

        'createThread.' . Thread::TYPE_OF_TEXT . '.anonymous'       => PermissionKey::THREAD_ALLOW_ANONYMOUS, // 发布文字匿名发布
        'createThread.' . Thread::TYPE_OF_LONG . '.anonymous'       => PermissionKey::THREAD_ALLOW_ANONYMOUS, // 发布长文匿名发布
        'createThread.' . Thread::TYPE_OF_VIDEO . '.anonymous'      => PermissionKey::THREAD_ALLOW_ANONYMOUS, // 发布视频匿名发布
        'createThread.' . Thread::TYPE_OF_IMAGE . '.anonymous'      => PermissionKey::THREAD_ALLOW_ANONYMOUS, // 发布图片匿名发布
        'createThread.' . Thread::TYPE_OF_AUDIO . '.anonymous'      => PermissionKey::THREAD_ALLOW_ANONYMOUS, // 发布语音匿名发布
        'createThread.' . Thread::TYPE_OF_QUESTION . '.anonymous'   => PermissionKey::THREAD_ALLOW_ANONYMOUS, // 发布问答匿名发布
        'createThread.' . Thread::TYPE_OF_GOODS . '.anonymous'      => PermissionKey::THREAD_ALLOW_ANONYMOUS, // 发布商品匿名发布

        'attachment.create.0'               => PermissionKey::THREAD_INSERT_ATTACHMENT, // 上传附件
        'attachment.create.1'               => PermissionKey::THREAD_INSERT_IMAGE, // 上传图片
        'createThreadPaid'                  => PermissionKey::THREAD_INSERT_PAY, // 插入付费
    ];

    //4.预备处理的老权限数据 删除key 增加value值
    public $upgradeOldPermission = [
        'createThread.' . Thread::TYPE_OF_IMAGE     => [PermissionKey::CREATE_THREAD,PermissionKey::THREAD_INSERT_IMAGE], // 发布图片
        'createThread.' . Thread::TYPE_OF_VIDEO     => [PermissionKey::CREATE_THREAD,PermissionKey::THREAD_INSERT_VIDEO], // 发布视频
        'createThread.' . Thread::TYPE_OF_AUDIO     => [PermissionKey::CREATE_THREAD,PermissionKey::THREAD_INSERT_AUDIO], // 发布语音
        'createThread.' . Thread::TYPE_OF_QUESTION  => [PermissionKey::CREATE_THREAD,PermissionKey::THREAD_INSERT_REWARD], // 发布问答
        'createThread.' . Thread::TYPE_OF_GOODS     => [PermissionKey::CREATE_THREAD,PermissionKey::THREAD_INSERT_GOODS], // 发布商品
        'thread.editOwnThreadOrPost'                => [PermissionKey::THREAD_EDIT_OWN,'switch.'.PermissionKey::THREAD_EDIT_OWN], // 编辑自己的主题或回复
    ];

    public function handle()
    {
        $this->info('脚本执行 [开始]');
        $this->info('');

        //1.直接删除的老权限
        $this->info('1.正在删除老权限数据');
        $deleteOldPermissionCount = Permission::query()->whereIn('permission',$this->deleteOldPermission)->count();
        if ($deleteOldPermissionCount != 0) {
            $deleteOldPermission = Permission::query()->whereIn('permission',$this->deleteOldPermission)->get();
            app('log')->info('被删除的权限数据：'.$deleteOldPermission);
            Permission::query()->whereIn('permission',$this->deleteOldPermission)->delete();
            $this->debugInfo('被删除的权限数据：'.$deleteOldPermission);
            $this->info('被删除的权限数据：'.$deleteOldPermissionCount.'条');
        } else {
            $this->info('被删除的权限数据：0条');
        }
        $this->info('');

        //2.需删除的分类权限
        $this->info('2.正在处理需删除的分类权限');
        $deleteOldCategoryPermission = [];
        $categoryIds = Category::query()->pluck('id');
        foreach ($categoryIds as $categoryId) {
            array_push($deleteOldCategoryPermission,'category'.$categoryId.'.createThread');
            array_push($deleteOldCategoryPermission,'category'.$categoryId.'.freeViewPosts.2');
            array_push($deleteOldCategoryPermission,'category'.$categoryId.'.freeViewPosts.3');
            array_push($deleteOldCategoryPermission,'category'.$categoryId.'.freeViewPosts.4');
            array_push($deleteOldCategoryPermission,'category'.$categoryId.'.freeViewPosts.5');
            array_push($deleteOldCategoryPermission,'category'.$categoryId.'.editPosts');
        }
        $deleteOldCategoryPermissionCount = Permission::query()->whereIn('permission',$deleteOldCategoryPermission)->count();
        if ($deleteOldCategoryPermissionCount != 0) {
            $deleteOldCategoryPermission = Permission::query()->whereIn('permission',$deleteOldCategoryPermission)->get();
            $deleteOldCategoryPermissionData = [];
            foreach ($deleteOldCategoryPermission as $value){
                array_push($deleteOldCategoryPermissionData,$value['permission']);
            }
            app('log')->info('被删除的分类权限数据：'.$deleteOldCategoryPermission);
            Permission::query()->whereIn('permission',$deleteOldCategoryPermissionData)->delete();
            $this->debugInfo('被删除的分类权限数据：'.$deleteOldCategoryPermission);
            $this->info('被删除的分类权限数据：'.$deleteOldCategoryPermissionCount.'条');
        } else {
            $this->info('被删除的分类权限数据：0条');
        }
        $this->info('');

        //3.将key 重命名为 value的权限
        $this->info('3.正在重命名旧权限数据');
        $renameOldPermission = array_keys($this->renameOldPermission);
        $renameOldPermissionCount = Permission::query()->whereIn('permission',$renameOldPermission)->count();
        if ($renameOldPermissionCount != 0) {
            $renameOldPermission = Permission::query()->whereIn('permission',$renameOldPermission)->get();
            foreach ($renameOldPermission as $oldPermission){
                $renameOldPermissionData = ['group_id'=>$oldPermission->group_id,'permission'=>$oldPermission->permission];
                $renameNewPermissionData = ['group_id'=>$oldPermission->group_id,'permission'=>$this->renameOldPermission[$oldPermission->permission]];
                $exists = Permission::query()->where($renameNewPermissionData)->exists();
                if (!$exists) {
                    app('log')->info('权限数据：'.collect($oldPermission).' 被重命名为：'.collect($renameNewPermissionData));
                    Permission::query() ->where($renameOldPermissionData)
                                        ->update($renameNewPermissionData);
                    $this->debugInfo('权限数据：'.collect($oldPermission).' 被重命名为：'.collect($renameNewPermissionData));
                } else {
                    app('log')->info('需重命名为：'.collect($renameNewPermissionData).' 的权限数据已存在，该权限：'
                                     .collect($renameOldPermissionData).' 已被删除');
                    Permission::query()->where($renameOldPermissionData)->delete();
                    $this->debugInfo('需重命名为：'.collect($renameNewPermissionData).' 的权限数据已存在，该权限：'
                                     .collect($renameOldPermissionData).' 已被删除');
                }
            }
            $this->info('需重命名的权限数据：'.$renameOldPermissionCount.'条');
        } else {
            $this->info('需重命名的权限数据：0条');
        }
        $this->info('');

        //4.预备处理的老权限数据 删除key 增加value值
        $this->info('4.正在处理的旧权限数据');
        $upgradeOldPermission = array_keys($this->upgradeOldPermission);
        $upgradeOldPermissionCount = Permission::query()->whereIn('permission',$upgradeOldPermission)->count();
        if ($upgradeOldPermissionCount != 0) {
            $upgradeOldPermission = Permission::query()->whereIn('permission',$upgradeOldPermission)->get();
            foreach ($upgradeOldPermission as $value){
                $upgradeOldPermissionData = ['group_id'=>$value->group_id,'permission'=>$value->permission];
                app('log')->info('旧权限数据：'.collect($upgradeOldPermissionData).' 已被删除');
                Permission::query()->where($upgradeOldPermissionData)->delete();
                $this->debugInfo('旧权限数据：'.collect($upgradeOldPermissionData).' 已被删除');
                foreach ($this->upgradeOldPermission[$value->permission] as $newValue){
                    $upgradeNewPermissionData = ['group_id'=>$value->group_id,'permission'=>$newValue];
                    $exists = Permission::query()->where($upgradeNewPermissionData)->exists();
                    if (!$exists) {
                        app('log')->info('旧权限数据被替换为的新权限数据为：'.collect($upgradeNewPermissionData));
                        Permission::query()->insert($upgradeNewPermissionData);
                        $this->debugInfo('旧权限数据被替换为的新权限数据为：'.collect($upgradeNewPermissionData));
                    } else {
                        app('log')->info('旧权限数据被替换为新的权限数据：'.collect($upgradeNewPermissionData).' 已存在,不添加新数据：');
                        $this->debugInfo('旧权限数据被替换为新的权限数据：'.collect($upgradeNewPermissionData).' 已存在,不添加新数据：');
                    }
                }
                $this->info('');
            }
            $this->info('需处理的旧权限数据：'.$upgradeOldPermissionCount.'条');
        } else {
            $this->info('需处理的旧权限数据：0条');
        }

        $this->info('');
        $this->info('脚本执行 [完成]');
    }

    public function debugInfo($info){
        if ($this->debugInfo) {
            $this->info($info);
        }
    }
}
