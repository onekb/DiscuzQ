<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use App\Models\Category;
use App\Models\Group;
use App\Models\Permission;
use App\Models\Thread;
use Discuz\Console\AbstractCommand;

class UpdateCategoryPermission extends AbstractCommand
{
    /**
     * @var string
     */
    protected $signature = 'upgrade:split-permissions';

    /**
     * @var string
     */
    protected $description = 'Update category permissions.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        /**
         * 新增权限
         */

        $categoryIds = Category::query()->pluck('id');

        $allowCreateTypes = [
            'createThread' => 'createThread.' . Thread::TYPE_OF_TEXT,
            'createThreadLong' => 'createThread.' . Thread::TYPE_OF_LONG,
            'createThreadVideo' => 'createThread.' . Thread::TYPE_OF_VIDEO,
            'createThreadImage' => 'createThread.' . Thread::TYPE_OF_IMAGE,
            'createThreadAudio' => 'createThread.' . Thread::TYPE_OF_AUDIO,
            'createThreadQuestion' => 'createThread.' . Thread::TYPE_OF_QUESTION,
            'createThreadGoods' => 'createThread.' . Thread::TYPE_OF_GOODS,
        ];

        // 之前分类下的 replyThread 权限改为 thread.reply 权限
        $canReplyThread = Permission::query()->where('permission', 'like', '%.replyThread')->get()
            ->map(function (Permission $permission) {
                return [
                    'group_id' => $permission->group_id,
                    'permission' => str_replace('replyThread', 'thread.reply', $permission->permission),
                ];
            });

        // 能浏览分类列表的组，为其添加新拆分的能浏览分类详情的权限
        $canViewPosts =  Permission::query()->where('permission', 'like', '%.viewThreads')->get()
            ->map(function (Permission $permission) {
                return [
                    'group_id' => $permission->group_id,
                    'permission' => str_replace('viewThreads', 'thread.viewPosts', $permission->permission),
                ];
            });

        // rename canCreateThread
        $canCreateThread = Permission::query()->where('permission', 'like', 'createThread%')->get()
            ->map(function (Permission $permission) use ($allowCreateTypes) {
                if (isset($allowCreateTypes[$permission->permission])) {
                    return [
                        'group_id' => $permission->group_id,
                        'permission' => $allowCreateTypes[$permission->permission],
                    ];
                } else {
                    return [];
                }
            });

        // canBeReward -> category{$categoryId}.thread.canBeReward
        $canBeReward = Permission::query()->where('permission', 'canBeReward')->pluck('group_id')
            ->crossJoin($categoryIds)->map(function ($permission) {
                return [
                    'group_id' => $permission[0],
                    'permission' => "category{$permission[1]}.thread.canBeReward",
                ];
            });

        // editOwnThreadOrPost -> category{$categoryId}.thread.editOwnThreadOrPost
        $editOwnThreadOrPost = Permission::query()->where('permission', 'editOwnThreadOrPost')->pluck('group_id')
            ->crossJoin($categoryIds)->map(function ($permission) {
                return [
                    'group_id' => $permission[0],
                    'permission' => "category{$permission[1]}.thread.editOwnThreadOrPost",
                ];
            });

        // hideOwnThreadOrPost -> category{$categoryId}.thread.hideOwnThreadOrPost
        $hideOwnThreadOrPost = Permission::query()->where('permission', 'hideOwnThreadOrPost')->pluck('group_id')
            ->crossJoin($categoryIds)->map(function ($permission) {
                return [
                    'group_id' => $permission[0],
                    'permission' => "category{$permission[1]}.thread.hideOwnThreadOrPost",
                ];
            });

        $permissions = collect()
            ->merge($canReplyThread)
            ->merge($canViewPosts)
            ->merge($canCreateThread)
            ->merge($canBeReward)
            ->merge($editOwnThreadOrPost)
            ->merge($hideOwnThreadOrPost);

        $defaultPermissions = [
            'switch.viewThreads',                 // 查看帖子列表
            'switch.createThread',                // 发布帖子
            'switch.thread.reply',                // 回复帖子
            'switch.thread.viewPosts',            // 查看详情
            'switch.thread.canBeReward',          // 是否允许被打赏
        ];

        Group::query()->where('id', '>=', Group::MEMBER_ID)->get()
            ->map(function (Group $group) use (&$permissions, $defaultPermissions) {
                $newPermissions = [];

                foreach ($defaultPermissions as $permission) {
                    $newPermissions[] = [
                        'group_id' => $group->id,
                        'permission' => $permission,
                    ];
                }

                $permissions = $permissions->merge($newPermissions);
            });

        // 游客看帖和看详情
        $permissions->merge([
            [
                'group_id' => Group::GUEST_ID,
                'permission' => 'switch.viewThreads',
            ],
            [
                'group_id' => Group::GUEST_ID,
                'permission' => 'switch.thread.viewPosts',
            ],
        ]);

        // 插入或忽略
        Permission::query()->insertOrIgnore($permissions->filter()->toArray());

        /**
         * 删除权限
         */

        Permission::query()
            ->where('permission', 'like', '%.replyThread')
            ->orWhereIn('permission', [
                'canBeReward',
                'viewUserList',
                'thread.batchEdit',
                'createThreadLong',
                'createThreadVideo',
                'createThreadImage',
                'createThreadAudio',
                'createThreadQuestion',
                'createThreadGoods',
                'editOwnThreadOrPost',
                'hideOwnThreadOrPost',
            ])->delete();

        $this->info('success');
    }
}
