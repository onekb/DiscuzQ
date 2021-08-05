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
use App\Events\Category\Created;
use Carbon\Carbon;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqModel;
use Discuz\Database\ScopeVisibilityTrait;
use Discuz\Foundation\EventGeneratorTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property int $sort
 * @property int $property
 * @property int $thread_count
 * @property array $moderators
 * @property string $ip
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $parentid
 */
class Category extends DzqModel
{
    use EventGeneratorTrait;
    use ScopeVisibilityTrait;

    /**
     * {@inheritdoc}
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * 分类权限
     *
     * @var array
     */
    public static $categoryPermissions = [
        'viewThreads',                 // 查看帖子列表
        'createThread',                // 发布帖子
        'thread.reply',                // 回复帖子
        'thread.edit',                 // 编辑帖子
        'thread.hide',                 // 删除帖子
        'thread.essence',              // 加精帖子
        'thread.viewPosts',            // 查看详情
        'thread.editPosts',            // 编辑回复
        'thread.hidePosts',            // 删除回复
        'thread.canBeReward',          // 是否允许被打赏
        'thread.editOwnThreadOrPost',  // 编辑自己主题或回复的权限
        'thread.hideOwnThreadOrPost',  // 删除自己主题或回复的权限
        'thread.freeViewPosts.1',      // 免费查看付费帖子
        'thread.freeViewPosts.2',      // 免费查看付费视频
        'thread.freeViewPosts.3',      // 免费查看付费图片
        'thread.freeViewPosts.4',      // 免费查看付费语音
        'thread.freeViewPosts.5',      // 免费查看付费问答

        'createThread',               // 发布帖子
        'insertImage',              // 插入图片
        'insertVideo',             // 插入视频
        'insertAudio',             // 插入语音
        'insertDoc',             // 插入附件
        'insertGoods',             // 插入商品
        'insertPay',             // 插入付费
        'insertReward',             // 插入悬赏
        'insertRedPacket',             // 插入红包
        'insertPosition',             // 插入位置
    ];

    /**
     * Create a new category.
     *
     * @param string $name
     * @param string $description
     * @param int $sort
     * @param string $icon
     * @param string $ip
     * @return static
     */
    public static function build(string $name, string $description, int $sort, int $parentid, string $icon = '', string $ip = '')
    {
        $category = new static;

        $category->name = $name;
        $category->description = $description;
        $category->sort = $sort;
        $category->parentid = $parentid;
        $category->icon = $icon;
        $category->ip = $ip;

        $category->raise(new Created($category));

        return $category;
    }

    /**
     * @param string $value
     * @return array
     */
    public function getModeratorsAttribute($value)
    {
        return explode(',', $value);
    }

    /**
     * @param $value
     */
    public function setModeratorsAttribute($value)
    {
        $this->attributes['moderators'] = is_array($value) ? implode(',', $value) : $value;
    }

    /**
     * Refresh the thread's comments count.
     *
     * @return $this
     */
    public function refreshThreadCount()
    {
        $category_ids = Category::query()->where('id', $this->id)->orWhere('parentid', $this->id)->pluck('id')->toArray();
        $this->thread_count = Thread::query()
            ->where('is_approved', Thread::APPROVED)
            ->where('is_draft', 0)
            ->whereIn('category_id', $category_ids)
            ->whereNull('deleted_at')
            ->whereNotNull('user_id')
            ->where('is_display', Thread::BOOL_YES)
            ->count();

        $categoryDetail = Category::query()->where('id', $this->id)->first();
        if ($categoryDetail->parentid !== 0) {
            $father_category_ids = Category::query()->where('id', $categoryDetail->parentid)->orWhere('parentid', $categoryDetail->parentid)->pluck('id')->toArray();
            $categoryFatherDetail = Category::query()->where('id', $categoryDetail->parentid)->first();
            $categoryFatherDetail->thread_count = Thread::query()
                ->where('is_approved', Thread::APPROVED)
                ->where('is_draft', 0)
                ->whereIn('category_id', $father_category_ids)
                ->whereNull('deleted_at')
                ->whereNotNull('user_id')
                ->where('is_display', Thread::BOOL_YES)
                ->count();
            $categoryFatherDetail->save();
        }

        return $this;
    }

    public static function refreshThreadCountV3($categoryId)
    {
        $categoryDetail = Category::query()->where('id', $categoryId)->first();
        if (empty($categoryDetail)) {
            return false;
        }
        $category_ids = Category::query()->where('id', $categoryId)->orWhere('parentid', $categoryId)->pluck('id')->toArray();
        $categoryDetail->thread_count = Thread::query()
            ->where('is_approved', Thread::APPROVED)
            ->where('is_draft', 0)
            ->whereIn('category_id', $category_ids)
            ->whereNull('deleted_at')
            ->whereNotNull('user_id')
            ->where('is_display', Thread::BOOL_YES)
            ->count();
        $categoryDetail->save();
        if ($categoryDetail->parentid !== 0) {
            $father_category_ids = Category::query()->where('id', $categoryDetail->parentid)->orWhere('parentid', $categoryDetail->parentid)->pluck('id')->toArray();
            $categoryFatherDetail = Category::query()->where('id', $categoryDetail->parentid)->first();
            $categoryFatherDetail->thread_count = Thread::query()
                ->where('is_approved', Thread::APPROVED)
                ->where('is_draft', 0)
                ->whereIn('category_id', $father_category_ids)
                ->whereNull('deleted_at')
                ->whereNotNull('user_id')
                ->where('is_display', Thread::BOOL_YES)
                ->count();
            $categoryFatherDetail->save();
        }
        return true;
    }

    /**
     * Define the relationship with the category's threads.
     *
     * @return HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * @param User $user
     * @param string $permission
     * @param bool $condition
     * @return array
     */
    protected static function getIdsWherePermission(User $user, string $permission, bool $condition = true): array
    {
        static $categories;

        if (!$categories) {
            $categories = static::all();
        }

        $hasGlobalPermission = $user->hasPermission($permission);

        $canForCategory = function (self $category) use ($user, $permission, $hasGlobalPermission) {
            return $user->hasPermission('switch.' . $permission)
                && ($hasGlobalPermission || $user->hasPermission('category' . $category->id . '.' . $permission));
        };

        $ids = [];

        foreach ($categories as $category) {
            if ($canForCategory($category) === $condition) {
                $ids[] = $category->id;
            }
        }

        return $ids;
    }

    public static function getIdsWhereCan(User $user, string $permission): array
    {
        return static::getIdsWherePermission($user, $permission, true);
    }

    public static function getIdsWhereCannot(User $user, string $permission): array
    {
        return static::getIdsWherePermission($user, $permission, false);
    }

    public function hasThreads($id, $status)
    {
        $category = Category::query()->findOrFail($id);

        $childCategoryIds = array();
        if ($category->parentid == 0) {
            $childCategoryIds = Category::query()->where('parentid', $category->id)->pluck('id')->toArray();
        }
        $categoryIds = array_merge([$id], $childCategoryIds);

        $query = Thread::query();
        $query->whereIn('category_id', $categoryIds);
        $query->where('is_approved', Thread::APPROVED);
        $query->where('is_draft', Thread::IS_NOT_DRAFT);
        $query->whereNotNull('user_id');
        if ($status == 'normal') {
            $query->whereNull('deleted_at');
        } else {
            $query->whereNotNull('deleted_at');
        }
        $threads = $query->get()->toArray();

        if ($threads) {
            return true;
        }
        return false;
    }

    public function getValidCategoryIds(User $user, $categoryids = [])
    {
        $groups = $user->groups->toArray();
        if (empty($groups)) {
            return false;
        }
        $permissions = Permission::getUserPermissions($user);
        $categories = self::getCategories();
        $cids = array_column($categories, 'id');
        $p = [];
        if ($user->isAdmin()) {
            $p = $cids;
        } else {
            foreach ($cids as $cid) {
                $viewThread = 'category' . $cid . '.viewThreads';
                in_array($viewThread, $permissions) && $p[] = $cid;
            }
            if (in_array('viewThreads', $permissions)) {
                $p = $cids;
            }
        }
        if (empty($categoryids)) {
            $categoryids = $p;
        } else {
            $categoryids = array_intersect($categoryids, $p);
        }
        return $categoryids;
    }


    /**
     * @desc 获取所有分类
     */
    public static function getCategories()
    {
        if (app()->has(CacheKey::CATEGORIES)) {
            return app()->get(CacheKey::CATEGORIES);
        }
        $cache = app('cache');
        $categories = $cache->get(CacheKey::CATEGORIES);
        if ($categories) {
            app()->instance(CacheKey::CATEGORIES, $categories);
            return $categories;
        }
        $categories = self::query()->get()->toArray();
        app()->instance(CacheKey::CATEGORIES, $categories);
        $cache->put(CacheKey::CATEGORIES, $categories, 60 * 60);
        return $categories;
    }

    protected function clearCache()
    {
        DzqCache::delKey(CacheKey::CATEGORIES);
    }
}
