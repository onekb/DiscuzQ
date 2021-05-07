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

namespace App\Commands\Category;

use App\Events\Category\Deleting;
use App\Models\Category;
use App\Models\User;
use App\Models\AdminActionLog;
use App\Repositories\CategoryRepository;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Foundation\EventsDispatchTrait;
use Exception;
use Illuminate\Contracts\Events\Dispatcher;
use Psr\Http\Message\ServerRequestInterface;

class DeleteCategory
{
    use AssertPermissionTrait;
    use EventsDispatchTrait;

    /**
     * The ID of the category to delete.
     *
     * @var int
     */
    public $categoryId;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * 暂未用到，留给插件使用
     *
     * @var array
     */
    public $data;

    /**
     * @param $categoryId
     * @param User $actor
     * @param array $data
     */
    public function __construct($categoryId, User $actor, array $data = [])
    {
        $this->categoryId = $categoryId;
        $this->actor = $actor;
        $this->data = $data;
    }

    /**
     * @param Dispatcher $events
     * @param CategoryRepository $categories
     * @return Category
     * @throws PermissionDeniedException
     * @throws Exception
     */
    public function handle(Dispatcher $events, CategoryRepository $categories, ServerRequestInterface $request)
    {
        $this->events = $events;

        $category = $categories->findOrFail($this->categoryId, $this->actor);

        $this->assertCan($this->actor, 'delete', $category);

        // 分类下有正常主题时不能删除
        if ($category->hasThreads($category->id, 'normal')) {
            throw new Exception('cannot_delete_category_with_threads');
        }

        // 分类下有回收站的主题时不能删除
        if ($category->hasThreads($category->id, 'delete')) {
            throw new Exception('cannot_delete_recycle_category_with_threads');
        }

        if ($category['parentid'] == 0) {
            $son_list = Category::query()->where('parentid',$this->categoryId)->get()->toArray();
            if (isset($son_list) && !empty($son_list)) {
                foreach ($son_list as $key => $value) {
                    $son_category = $categories->findOrFail($value['id'], $this->actor);
                    if (!empty($son_category)) {
                        if ($son_category->hasThreads($son_category->id, 'normal')) {
                            throw new Exception('cannot_delete_category_with_threads');
                        }
                        if ($son_category->hasThreads($son_category->id, 'delete')) {
                            throw new Exception('cannot_delete_recycle_category_with_threads');
                        }
                    }
                }
            }
        }

        $name = $category['name'];

        $this->events->dispatch(
            new Deleting($category, $this->actor, $this->data)
        );

        $category->delete();

        AdminActionLog::createAdminActionLog(
            $this->actor->id,
            '删除内容分类【'. $name .'】'
        );

        $this->dispatchEventsFor($category, $this->actor);

        return $category;
    }
}
