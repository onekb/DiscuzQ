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
use App\Repositories\SequenceRepository;
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
use Carbon\Carbon;
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

        $thread = Thread::query()->where('id',$this->threadId)->first();
        $action_desc = '';

        if($thread->title == '' || empty($thread->title)) {
            $threadTitle = '，其ID为'. $thread->id;
        }else{
            $threadTitle = '【'. $thread->title .'】';
        }

        if (isset($attributes['isSticky'])) {
            if ($thread->is_sticky != $attributes['isSticky']) {
                $thread->is_sticky = $attributes['isSticky'];
                $thread->updated_at = Carbon::now();
                if ($thread->is_sticky) {
                    $this->threadNotices($thread, $this->actor, 'isSticky', $attributes['message'] ?? '');
                }
            }
        }

        if (isset($attributes['isEssence'])) {
            if ($thread->is_essence != $attributes['isEssence']) {
                $thread->is_essence = $attributes['isEssence'];

                if ($thread->is_essence) {
                    $this->threadNotices($thread, $this->actor, 'isEssence', $attributes['message'] ?? '');
                }
            }
        }

        if (isset($attributes['isDeleted'])) {
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
        if(!isset($attributes['isFavorite']) && !isset($attributes['isSticky']) && !isset($attributes['isEssence'])){
            app(SequenceRepository::class)->updateSequenceCache($this->threadId, 'edit');
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
