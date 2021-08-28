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
use App\Models\Thread;
use App\Models\User;
use App\Notifications\Messages\Database\PostMessage;
use App\Notifications\Related;
use App\Notifications\Replied;
use App\Notifications\System;

/**
 * Post 发送通知
 * Trait PostNoticesTrait
 *
 * @package App\Traits
 */
trait PostNoticesTrait
{
    /**
     * 发送通知
     *
     * @param Post $post
     * @param User $actor
     * @param string $type
     * @param string $message
     */
    public function postNotices(Post $post, User $actor, $type, $message = '')
    {
        // 无需给自己发送通知
        if ($post->user_id == $actor->id) {
            return;
        }
        $message = $message ?: '无';

        switch ($type) {
            case 'isApproved':  // 内容审核通知
                $this->postisapproved($post, $actor, ['refuse' => $message]);
                break;
            case 'isDeleted':   // 内容删除通知
                $this->postIsDeleted($post, $actor, ['refuse' => $message]);
                break;
        }
    }

    /**
     * 发送@通知
     *
     * @param Post $post
     * @param User $actor 并非当前登录用户,这里是发帖人
     */
    public function sendRelated(Post $post, User $actor)
    {
        if (empty($post->content)) {
            return;
        }

        $newsNameArr = [];
        preg_match_all('/<span.*>(.*)<\/span>/isU', $post->content, $newsNameArr);
        $newsNameArr = $newsNameArr[1];

        if (empty($newsNameArr)) {
            $usernameArray=explode(' ', $post->content);
            if (!empty($usernameArray)) {
                foreach ($usernameArray as $value) {
                    if (strpos($value, '@') !== false) {
                        $newsNameArr[] = $value;
                    }
                }
            }
        }

        if (empty($newsNameArr)) {
            return;
        }

        $newsNameArr2 = [];
        foreach ($newsNameArr as $v) {
            $string = trim(substr($v, 1));
            if ($actor->nickname != $string) {
                $newsNameArr2[] = $string;
            }
        }

        $users = User::query()->whereIn('nickname', $newsNameArr2)->get();
        if (empty($users)) {
            return;
        }

        $post->mentionUsers()->sync(array_column($users->toArray(), 'id'));

        $users->load('deny');
        $users->filter(function ($user) use ($post) {
            //把作者拉黑的用户不发通知
            return ! in_array($post->user_id, array_column($user->deny->toArray(), 'id'));
        })->each(function (User $user) use ($post, $actor) {
            // Tag 发送通知
            if ($post->is_approved == Post::UNAPPROVED) {
                return;
            }
            $user->notify(new Related($user, $post));
        });
    }

    /**
     * 内容删除通知
     *
     * @param $post
     * @param $actor
     * @param array $attach 原因
     */
    private function postIsDeleted($post, $actor, array $attach)
    {
        $post = Post::changeNotifitionPostContent($post);
        $data = [
            'message' => $post->formatContent(), // 解析表情
            'post' => $post,
            'refuse' => $attach['refuse'],
            'notify_type' => Post::NOTIFY_DELETE_TYPE,
        ];

        // Tag 发送通知
        $post->user->notify(new System(PostMessage::class, $actor, $data));
    }

    /**
     * 内容审核通知
     *
     * @param $post
     * @param $actor
     * @param array $attach 原因
     */
    private function postIsApproved($post, $actor, array $attach)
    {
        $data = [
            'message' => $this->getPostTitle($post),
            'post' => $post,
            'refuse' => $attach['refuse'],
        ];

        if ($post->is_approved == 1) {
            // 发送通过通知
            $data = array_merge($data, ['notify_type' => Post::NOTIFY_APPROVED_TYPE]);

            // 发送回复人的主题通知 (回复自己主题不发送通知)
            if ($post->user_id != $post->thread->user_id) {
                // Tag 发送通知
                $post->thread->user->notify(new Replied($actor, $post, ['notify_type' => 'notify_approved']));
            }
        } elseif ($post->is_approved == 2) {
            // 忽略就发送不通过通知
            $data = array_merge($data, ['notify_type' => Post::NOTIFY_UNAPPROVED_TYPE]);
        }

        // Tag 发送通知
        $post->user->notify(new System(PostMessage::class, $actor, $data));
    }

    /**
     * 标题内容替换
     *
     * @param $post
     * @return mixed
     */
    public function getPostTitle(Post $post)
    {
        return $post->thread->type === Thread::TYPE_OF_LONG ? $post->thread->title : $post->content;
    }
}
