<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use App\Models\Category;
use App\Models\Group;
use App\Models\Permission;
use Discuz\Console\AbstractCommand;
use Illuminate\Support\Collection;

class InitCategoryPermission extends AbstractCommand
{
    /**
     * @var string
     */
    protected $signature = 'upgrade:category-permission';

    /**
     * @var string
     */
    protected $description = 'Initialize category permissions.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $categoryIds = Category::query()->pluck('id');
        $groupIds = Group::query()->where('id', '>=', Group::MEMBER_ID)->pluck('id');

        // 游客权限
        $guestPermission = $categoryIds->map(function ($categoryId) {
            return [
                'group_id' => Group::GUEST_ID,
                'permission' => "category{$categoryId}.viewThreads",
            ];
        });

        /** @var Collection $permissions */
        $permissions = $groupIds->crossJoin($categoryIds)->reduce(function (Collection $carry, $idArray) {
            [$groupId, $categoryId] = $idArray;

            return $carry->merge([
                [
                    'group_id' => $groupId,
                    'permission' => "category{$categoryId}.viewThreads",
                ],
                [
                    'group_id' => $groupId,
                    'permission' => "category{$categoryId}.createThread",
                ],
                [
                    'group_id' => $groupId,
                    'permission' => "category{$categoryId}.replyThread",
                ],
            ]);
        }, $guestPermission);

        Permission::query()->insertOrIgnore($permissions->toArray());

        $this->info('success');
    }
}
