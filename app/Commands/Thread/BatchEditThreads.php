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

namespace App\Commands\Thread;

use App\Events\Thread\Saving;
use App\Events\Thread\ThreadWasApproved;
use App\Models\Thread;
use App\Models\User;
use App\Models\Category;
use App\Models\AdminActionLog;
use App\Repositories\ThreadRepository;
use App\Traits\ThreadNoticesTrait;
use Discuz\Foundation\EventsDispatchTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class BatchEditThreads
{
    use EventsDispatchTrait;
    use ThreadNoticesTrait;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes to update on the threads.
     *
     * @var array
     */
    public $data;

    /**
     * @param User $actor
     * @param array $data
     */
    public function __construct(User $actor, array $data)
    {
        $this->actor = $actor;
        $this->data = $data;
    }

    /**
     * @param Dispatcher $events
     * @param ThreadRepository $threads
     * @return array
     */
    public function handle(Dispatcher $events, ThreadRepository $threads)
    {
        $this->events = $events;

        $result = ['data' => [], 'meta' => []];
        $titles = array();
        foreach ($this->data as $data) {
            if (isset($data['id'])) {
                $id = $data['id'];
            } else {
                continue;
            }

            /** @var Thread $thread */
            $thread = $threads->query()->whereVisibleTo($this->actor)->find($id);

            if ($thread) {
                $thread->timestamps = false;
            } else {
                $result['meta'][] = ['id' => $id, 'message' => 'model_not_found'];
                continue;
            }

            $attributes = Arr::get($data, 'attributes', []);
            $relationships = Arr::get($data, 'relationships', []);
            $category_id = $relationships['category']['data']['id'];
            if($thread->title !== ''){
                $titles[] = '【'. $thread->title .'】';
            }else{
                $titles[] = '【无标题，ID为'. $id .'】';
            }
            
            if (isset($attributes['isApproved']) && $attributes['isApproved'] < 3) {
                if ($this->actor->can('approve', $thread)) {
                    if ($thread->is_approved != $attributes['isApproved']) {
                        $thread->is_approved = $attributes['isApproved'];

                        $thread->raise(
                            new ThreadWasApproved($thread, $this->actor, ['message' => $attributes['message'] ?? ''])
                        );
                    }
                } else {
                    $result['meta'][] = ['id' => $id, 'message' => 'permission_denied'];
                    continue;
                }
            }

            if (isset($attributes['isSticky'])) {
                if ($this->actor->can('sticky', $thread)) {
                    if ($thread->is_sticky != $attributes['isSticky']) {
                        $thread->is_sticky = $attributes['isSticky'];

                        if ($thread->is_sticky) {
                            $this->threadNotices($thread, $this->actor, 'isSticky', $attributes['message'] ?? '');
                        }
                    }
                } else {
                    $result['meta'][] = ['id' => $id, 'message' => 'permission_denied'];
                    continue;
                }
            }

            if (isset($attributes['isSite'])) {
                if ($this->actor->can('isSite', $thread)) {
                    if ($thread->is_site != $attributes['isSite']) {
                        $thread->is_site = $attributes['isSite'];
                    }
                } else {
                    $result['meta'][] = ['id' => $id, 'message' => 'permission_denied'];
                    continue;
                }
            }

            if (isset($attributes['isEssence'])) {
                if ($this->actor->can('essence', $thread)) {
                    if ($thread->is_essence != $attributes['isEssence']) {
                        $thread->is_essence = $attributes['isEssence'];

                        if ($thread->is_essence) {
                            $this->threadNotices($thread, $this->actor, 'isEssence', $attributes['message'] ?? '');
                        }
                    }
                } else {
                    $result['meta'][] = ['id' => $id, 'message' => 'permission_denied'];
                    continue;
                }
            }

            if (isset($attributes['isDeleted'])) {
                if ($this->actor->can('hide', $thread)) {
                    $message = $attributes['message'] ?? '';

                    if ($attributes['isDeleted']) {
                        $thread->hide($this->actor, ['message' => $message]);
                    } else {
                        $thread->restore($this->actor, ['message' => $message]);
                    }
                } else {
                    $result['meta'][] = ['id' => $id, 'message' => 'permission_denied'];
                    continue;
                }
            }

            try {
                $this->events->dispatch(
                    new Saving($thread, $this->actor, $data)
                );
            } catch (\Exception $e) {
                $result['meta'][] = ['id' => $id, 'message' => $e->getMessage()];
                continue;
            }

            $thread->save();

            $result['data'][] = $thread;

            try {
                $this->dispatchEventsFor($thread, $this->actor);
            } catch (\Exception $e) {
                $result['meta'][] = ['id' => $id, 'message' => $e->getMessage()];
                continue;
            }
        }

        $titles = implode("、",$titles);

        if(isset($attributes['isApproved'])) {
            if($attributes['isApproved'] == Thread::APPROVED){
                $action_desc = '用户主题帖'. $titles .'通过审核';
            }
            if($attributes['isApproved'] == Thread::UNAPPROVED){
                $action_desc = '用户主题帖'. $titles .'暂被设为非法';
            }
            if($attributes['isApproved'] == Thread::IGNORED){
                $action_desc = '用户主题帖'. $titles .'被忽略';
            }
        }

        if(isset($attributes['isSticky'])) {
            if($attributes['isSticky'] == true){
                $action_desc = '批量置顶用户主题帖'. $titles;
            }else{
                $action_desc = '批量取消用户主题帖'. $titles .'的置顶';
            }
        }

        if(isset($attributes['isDeleted'])) {
            if ($attributes['isDeleted'] == true) {
                $action_desc = '批量删除用户主题帖'. $titles;
            } else {
                $action_desc = '批量还原用户主题帖'. $titles;
            }
        }

        if(isset($attributes['isEssence'])) {
            if($attributes['isEssence'] == true){
                $action_desc = '批量设置用户主题帖'. $titles .'为精华';
            }else{
                $action_desc = '批量取消用户主题帖'. $titles .'的精华标志';
            }
        }

        if(isset($attributes['isSite'])) {
            if($thread->is_site == true){
                $action_desc = '批量推荐用户主题帖'. $titles .'至付费首页';
            }else{
                $action_desc = '批量取消用户主题帖'. $titles .'的付费首页推荐';
            }
        }

        if($action_desc !== '' && !empty($action_desc)){
            AdminActionLog::createAdminActionLog(
                $this->actor->id,
                $action_desc
            );
            $status = 1;
        }

        if($status !== 1) {
            if($category_id !== '' && !empty($category_id)) {
                $categoryDetail = Category::query()->where('id', $category_id)->first();
                $action_desc = '批量转移用户主题帖'. $titles .'至【'. $categoryDetail['name'] .'】分类';
                AdminActionLog::createAdminActionLog(
                    $this->actor->id,
                    $action_desc
                );
            }
        }
        
        return $result;
    }
}
