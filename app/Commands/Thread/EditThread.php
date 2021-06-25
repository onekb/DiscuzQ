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

use App\Censor\Censor;
use App\Events\Post\Saved;
use App\Events\Thread\Deleting;
use App\Events\Thread\Saving;
use App\Events\Thread\ThreadWasApproved;
use App\Events\Thread\Updated;
use App\Models\Thread;
use App\Models\ThreadVideo;
use App\Models\User;
use App\Models\AdminActionLog;
use App\Repositories\ThreadRepository;
use App\Repositories\ThreadVideoRepository;
use App\Traits\ThreadNoticesTrait;
use App\Validators\ThreadValidator;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Foundation\EventsDispatchTrait;
use Exception;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class EditThread
{
    use AssertPermissionTrait;
    use EventsDispatchTrait;
    use ThreadNoticesTrait;

    /**
     * The ID of the thread to edit.
     *
     * @var int
     */
    public $threadId;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes to update on the thread.
     *
     * @var array
     */
    public $data;

    /**
     * @param int $threadId The ID of the thread to edit.
     * @param User $actor The user performing the action.
     * @param array $data The attributes to update on the thread.
     */
    public function __construct($threadId, User $actor, array $data)
    {
        $this->threadId = $threadId;
        $this->actor = $actor;
        $this->data = $data;
    }

    /**
     * @param Dispatcher $events
     * @param ThreadRepository $threads
     * @param Censor $censor
     * @param ThreadValidator $validator
     * @param ThreadVideoRepository $threadVideos
     * @param BusDispatcher $bus
     * @return Thread
     * @throws PermissionDeniedException
     * @throws ValidationException
     * @throws Exception
     */
    public function handle(Dispatcher $events, ThreadRepository $threads, Censor $censor, ThreadValidator $validator, ThreadVideoRepository $threadVideos, BusDispatcher $bus)
    {
        $this->events = $events;

        $attributes = Arr::get($this->data, 'attributes', []);

        $thread = $threads->findOrFail($this->threadId, $this->actor);
        $action_desc = '';

        if (isset($attributes['title'])) {
            $this->assertCan($this->actor, 'edit', $thread);

            // 敏感词校验
            $title = $censor->checkText($attributes['title']);

            // 存在审核敏感词时，将主题放入待审核
            if ($censor->isMod) {
                $thread->is_approved = Thread::UNAPPROVED;
            }

            $thread->title = $title;
        } else {
            // 不修改标题时，不更新修改时间
            $thread->timestamps = false;
        }

        if($thread->title == '' || empty($thread->title)) {
            $threadTitle = '，其ID为'. $thread->id;
        }else{
            $threadTitle = '【'. $thread->title .'】';
        }

        // 长文可以设置、编辑 附件价格
        if (isset($attributes['attachment_price']) && $thread->type == Thread::TYPE_OF_LONG) {
            $this->assertCan($this->actor, 'edit', $thread);

            if ($thread->attachment_price =  (float) $attributes['attachment_price']) {
                $this->assertCan($this->actor, 'createThreadPaid');
            }
        }

        // 文字贴与问答帖不能修改价格
        if (
            isset($attributes['price'])
            && $thread->type !== Thread::TYPE_OF_TEXT
            && $thread->type !== Thread::TYPE_OF_QUESTION
        ) {
            $this->assertCan($this->actor, 'edit', $thread);

            // 是否有权发布付费贴
            if ($thread->price = (float) $attributes['price']) {
                $this->assertCan($this->actor, 'createThreadPaid');
            }
        }

        if ($thread->price > 0 && isset($attributes['free_words'])) {
            $this->assertCan($this->actor, 'edit', $thread);
            $thread->free_words = (float)Arr::get($this->data, 'attributes.free_words', 0);
        }

        if (isset($attributes['longitude']) && isset($attributes['latitude'])) {
            $this->assertCan($this->actor, 'edit', $thread);

            $thread->longitude = (float) Arr::get($this->data, 'attributes.longitude', 0);
            $thread->latitude = (float) Arr::get($this->data, 'attributes.latitude', 0);
            $thread->address = Arr::get($this->data, 'attributes.address', '');
            $thread->location = Arr::get($this->data, 'attributes.location', '');
        }

        if (isset($attributes['isApproved']) && $attributes['isApproved'] < 3) {
            $this->assertCan($this->actor, 'approve', $thread);

            if ($thread->is_approved != $attributes['isApproved']) {
                $thread->is_approved = $attributes['isApproved'];

                if($attributes['isApproved'] == Thread::APPROVED){
                    $action_desc = '用户主题帖'. $threadTitle .'，通过审核';
                }
                if($attributes['isApproved'] == Thread::UNAPPROVED){
                    $action_desc = '用户主题帖'. $threadTitle .'，暂被设为非法';
                }
                if($attributes['isApproved'] == Thread::IGNORED){
                    $action_desc = '用户主题帖'. $threadTitle .'，被忽略';
                }

                $thread->raise(
                    new ThreadWasApproved($thread, $this->actor, ['message' => $attributes['message'] ?? ''])
                );
            }
        }

        if (isset($attributes['isSticky'])) {
            // $this->assertCan($this->actor, 'isSticky', $thread);

            if($this->actor->can('sticky', $thread)){
                if ($thread->is_sticky != $attributes['isSticky']) {
                    $thread->is_sticky = $attributes['isSticky'];

                    if ($thread->is_sticky) {
                        $this->threadNotices($thread, $this->actor, 'isSticky', $attributes['message'] ?? '');
                    }
                }
            }
        }

        if (isset($attributes['isSite'])) {
            $this->assertCan($this->actor, 'isSite', $thread);

            if ($thread->is_site != $attributes['isSite']) {
                $thread->is_site = $attributes['isSite'];
            }
        }

        if (isset($attributes['isEssence'])) {
            $this->assertCan($this->actor, 'essence', $thread);

            if ($thread->is_essence != $attributes['isEssence']) {
                $thread->is_essence = $attributes['isEssence'];

                if ($thread->is_essence) {
                    $this->threadNotices($thread, $this->actor, 'isEssence', $attributes['message'] ?? '');
                }
            }
        }

        if (isset($attributes['isDeleted'])) {
            $this->assertCan($this->actor, 'hide', $thread);

            if ($attributes['isDeleted']) {
                $thread->hide($this->actor, ['message' => $attributes['message'] ?? '']);
                $action_desc = '删除用户主题帖'. $threadTitle;
            } else {
                $thread->restore($this->actor, ['message' => $attributes['message'] ?? '']);
                $action_desc = '还原用户主题帖'. $threadTitle;
            }
        }

        $this->events->dispatch(
            new Saving($thread, $this->actor, $this->data)
        );
        $validator->valid($thread->getDirty());

        // 编辑视频帖或语音帖
        if ($fileId = Arr::get($this->data, 'attributes.file_id')) {
            /** @var ThreadVideo $threadVideo */
            $threadVideo = $threadVideos->findOrFailByThreadId($thread->id);

            if ($threadVideo->file_id != $fileId) {
                $threadVideoStatus = ThreadVideo::query()->where('file_id', $fileId)->first();
                if (empty($threadVideoStatus)) {
                    throw new Exception(trans('post.audio_video_not_null'));
                }
                if ($threadVideoStatus->status == Thread::THREAD_VIDEO_STATUS_TRANSCODING) {
                    throw new Exception(trans('post.audio_video_is_being_transcoded'));
                } else if ($threadVideoStatus->status == Thread::THREAD_VIDEO_STATUS_FAIL){
                    throw new Exception(trans('post.audio_video_transcoding_failed'));
                }

                // 将旧的视频或语音主题 id 设为 0
                $threadVideo->thread_id = 0;
                $threadVideo->save();

                // 创建新的视频或语音
                $video = $bus->dispatch(
                    new CreateThreadVideo($this->actor, $thread, $threadVideo->type, $this->data)
                );

                $threadVideo->type === ThreadVideo::TYPE_OF_VIDEO && $thread->setRelation('threadVideo', $video);
                $threadVideo->type === ThreadVideo::TYPE_OF_AUDIO && $thread->setRelation('threadAudio', $video);

                // 重新上传视频或语音修改为审核状态
//                $thread->is_approved = Thread::UNAPPROVED;
            }
        }

        $thread->raise(new Updated($thread, $this->actor, $this->data));

        $thread->save();
        if (isset($attributes['isDeleted'])){
            $this->events->dispatch(new Deleting($thread, $this->actor, $this->data));
        }

        if($action_desc !== '' && !empty($action_desc)){
            AdminActionLog::createAdminActionLog(
                $this->actor->id,
                $action_desc
            );
        }

        $this->dispatchEventsFor($thread, $this->actor);

        return $thread;
    }
}
