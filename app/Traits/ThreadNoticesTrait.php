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

namespace App\Traits;

use App\Models\Post;
use App\Models\Question;
use App\Models\Thread;
use App\Models\User;
use App\Notifications\Messages\Database\PostMessage;
use App\Notifications\Messages\Wechat\QuestionedWechatMessage;
use App\Notifications\Questioned;
use App\Notifications\System;

/**
 * Thread 发送通知
 * Trait ThreadNoticesTrait
 *
 * @package App\Traits
 */
trait ThreadNoticesTrait
{
    /**
     * 发送通知
     *
     * @param Thread $thread
     * @param User $actor
     * @param string $type
     * @param string $message
     */
    public function threadNotices(Thread $thread, User $actor, $type, $message = '')
    {
        // 审核通过时发送 @ 通知
        if (
            $type === 'isApproved'
            && $thread->is_approved === Thread::APPROVED
            && $thread->type == Thread::TYPE_OF_QUESTION
        ) {
            $this->sendRelated($thread->firstPost, $actor);
            /**
             * 如果是问答审核，发送回答者通知
             * (帖子合法才允许发送，向回答人发送问答通知)
             */
            // Tag 发送通知
            $thread->question->beUser->notify(new Questioned(QuestionedWechatMessage::class, $actor, $thread->question));
        }

        // 无需给自己发送通知
        if ($thread->user_id == $actor->id) {
            return;
        }

        $message = $message ?: '无';

        switch ($type) {
            case 'isEssence':   // 内容加精通知
                $this->sendIsEssence($thread, $actor);
                break;
            case 'isSticky':    // 内容置顶通知
                $this->sendIsSticky($thread, $actor);
                break;
            case 'isApproved':  // 内容审核通知
                $this->sendIsApproved($thread, $actor, ['refuse' => $message]);
                break;
            case 'isDeleted':   // 内容删除通知
                $this->sendIsDeleted($thread, $actor, ['refuse' => $message]);
                break;
        }
    }

    /**
     * @param Question $question
     * @param User $actor 主题创建人
     */
    public function sendQuestioned(Question $question, User $actor)
    {
        // Tag 发送通知 (帖子合法才允许发送，向回答人发送问答通知)
        $question->beUser->notify(new Questioned(QuestionedWechatMessage::class, $actor, $question));
    }

    /**
     * 内容置顶通知
     *
     * @param Thread $thread
     * @param User $actor
     */
    private function sendIsSticky($thread, $actor)
    {
        $build = [
            'message'     => $this->getThreadTitle($thread),
            'post'        => $thread->firstPost,
            'notify_type' => Post::NOTIFY_STICKY_TYPE,
        ];

        // Tag 发送通知
        $thread->user->notify(new System(PostMessage::class, $actor, $build));
    }

    /**
     * 内容精华通知
     *
     * @param Thread $thread
     * @param User $actor
     */
    private function sendIsEssence($thread, $actor)
    {
        $build = [
            'message'     => $this->getThreadTitle($thread),
            'post'        => $thread->firstPost,
            'notify_type' => Post::NOTIFY_ESSENCE_TYPE,
        ];

        // Tag 发送通知
        $thread->user->notify(new System(PostMessage::class, $actor, $build));
    }

    /**
     * 内容删除通知
     *
     * @param Thread $thread
     * @param User $actor
     * @param array $attach 原因
     */
    private function sendIsDeleted($thread, $actor, array $attach)
    {
        $data = [
            'message'     => $this->getThreadTitle($thread),
            'post'        => $thread->firstPost,
            'refuse'      => $attach['refuse'],
            'notify_type' => Post::NOTIFY_DELETE_TYPE,
        ];

        // Tag 发送通知
        $thread->user->notify(new System(PostMessage::class, $actor, $data));
    }

    /**
     * 内容审核通知
     *
     * @param Thread $thread
     * @param User $actor
     * @param array $attach 原因
     */
    private function sendIsApproved($thread, $actor, array $attach)
    {
        $data = [
            'message' => $this->getThreadTitle($thread),
            'post'    => $thread->firstPost,
            'refuse'  => $attach['refuse'],
        ];

        if ($thread->is_approved == 1) {
            // 发送通过通知
            $data = array_merge($data, ['notify_type' => Post::NOTIFY_APPROVED_TYPE]);
        } elseif ($thread->is_approved == 2) {
            // 忽略就发送不通过通知
            $data = array_merge($data, ['notify_type' => Post::NOTIFY_UNAPPROVED_TYPE]);
        }

        // Tag 发送通知
        $thread->user->notify(new System(PostMessage::class, $actor, $data));
    }

    /**
     * 首帖内容代替
     *
     * @param Thread $thread
     * @return mixed
     */
    public function getThreadTitle($thread)
    {
        return empty($thread->title) ? $thread->firstPost->content : $thread->title;
    }
}
