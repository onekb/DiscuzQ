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

namespace App\Commands\Dialog;

use App\Censor\Censor;
use App\Models\Attachment;
use App\Models\Dialog;
use App\Models\DialogMessage;
use App\Models\User;
use App\Repositories\DialogRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Foundation\EventsDispatchTrait;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

class CreateDialogMessage
{
    use EventsDispatchTrait;

    /**
     * @var
     */
    public $attributes;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * @param User $actor
     * @param $attributes
     */
    public function __construct(User $actor, $attributes)
    {
        $this->actor = $actor;
        $this->attributes = $attributes;
    }

    /**
     * @param DialogRepository $dialog
     * @param Dispatcher $events
     * @param Censor $censor
     * @return DialogMessage
     * @throws PermissionDeniedException
     * @throws InvalidConfigException
     * @throws GuzzleException
     * @throws Exception
     */
    public function handle(DialogRepository $dialog, Dispatcher $events, Censor $censor)
    {
        $this->events = $events;

        $dialog_id = Arr::get($this->attributes, 'dialog_id');

        $image_url = Arr::get($this->attributes, 'image_url', '');

        //敏感词检查
        $message_text = Arr::get($this->attributes, 'message_text');
        if (!empty($message_text)) {
            $message_text = trim($censor->checkText($message_text, 'dialog'));
        }

        /** @var Dialog $dialogRes */
        $dialogRes = $dialog->findOrFail($dialog_id, $this->actor);

        //在黑名单中，不能发消息
        if ($dialogRes->sender_user_id == $this->actor->id) {
            $user = $dialogRes->recipient;
        } else {
            $user = $dialogRes->sender;
        }
        if (!$user) {
            throw new ModelNotFoundException();
        }
        if (in_array($this->actor->id, array_column($user->deny->toArray(), 'id'))) {
            throw new PermissionDeniedException('已被屏蔽，不能发起私信对话');
        }

        $read_status = Arr::get($this->attributes, 'read_status',0);

        $attachment_id = Arr::get($this->attributes, 'attachment_id', 0);

        $status = Arr::get($this->attributes, 'status', DialogMessage::NORMAL_MESSAGE);

        if ($attachment_id) {
            $attachment = Attachment::query()
                    ->where('user_id', $this->actor->id)
                    ->where('type_id', 0)
                    ->where('type', Attachment::TYPE_OF_IMAGE)
                    ->where('id', $attachment_id)
                    ->first();
            if (!$attachment) {
                throw new Exception(trans('user.attachment_not_exist'));
            }
        }

        $message = [
            'message_text'  => $message_text,
            'image_url'     => $image_url
        ];

        $dialogMessage = DialogMessage::build($this->actor->id, $dialog_id, $attachment_id, $message, $read_status, $status);
        $dialogMessageRes = $dialogMessage->save();

        if ($dialogMessageRes) {
            //发送新消息后设置对方未读
            if ($dialogRes->sender_user_id == $this->actor->id) {
                $dialogRes->recipient_read_at = null;
            } else {
                $dialogRes->sender_read_at = null;
            }
            if ($status == 1) {
                $dialogRes->dialog_message_id = $dialogMessage->id;
            }

            $dialogRes->save();
        }

        return $dialogMessage;
    }
}
