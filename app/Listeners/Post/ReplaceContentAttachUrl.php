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

namespace App\Listeners\Post;

use App\Api\Controller\Posts\UpdatePostController;
use App\Api\Controller\Threads\CreateThreadController;
use App\Api\Controller\Threads\ResourceThreadController;
use App\Api\Controller\Threads\UpdateThreadController;
use App\Api\Controller\Threads\UpdateDraftController;
use App\Api\Serializer\AttachmentSerializer;
use App\Models\Attachment;
use App\Models\Post;
use App\Models\Thread;
use Discuz\Api\Events\WillSerializeData;
use s9e\TextFormatter\Utils;

class ReplaceContentAttachUrl
{
    public function handle(WillSerializeData $event)
    {
        if (
            $event->isController(ResourceThreadController::class)
            || $event->isController(CreateThreadController::class)
            || $event->isController(UpdateThreadController::class)
            || $event->isController(UpdateDraftController::class)
            || $event->isController(UpdatePostController::class)
        ) {
            // 图文混排需要替换插入文中的图片及附件地址
            if ($event->data instanceof Thread && $event->data->type === Thread::TYPE_OF_LONG) {
                $post = $event->data->firstPost;
            } elseif (
                $event->data instanceof Post
                && $event->data->is_first
                && $event->data->thread->type === Thread::TYPE_OF_LONG
            ) {
                $post = $event->data;
            } else {
                return;
            }

            if (!$post) {
                return;
            }

            /** @var AttachmentSerializer $attachmentSerializer */
            $attachmentSerializer = app(AttachmentSerializer::class);

            $attachmentSerializer->setRequest($event->request);

            // 所有图片及附件 URL
            $attachments = $post->images
                ->merge($post->attachments)
                ->keyBy('id')
                ->map(function (Attachment $attachment) use ($attachmentSerializer) {
                    if ($attachment->type === Attachment::TYPE_OF_IMAGE) {
                        return $attachmentSerializer->getDefaultAttributes($attachment)['thumbUrl'];
                    } elseif ($attachment->type === Attachment::TYPE_OF_FILE) {
                        return $attachmentSerializer->getDefaultAttributes($attachment)['url'];
                    }
                });

            // 数据原始内容，即 s9e 解析后的 XML
            $xml = $post->getRawOriginal('content');

            // 替换插入内容中的图片 URL
            $xml = Utils::replaceAttributes($xml, 'IMG', function ($attributes) use ($attachments) {
                if (isset($attributes['title']) && isset($attachments[$attributes['title']])) {
                    $attributes['src'] = $attachments[$attributes['title']];
                }

                return $attributes;
            });

            // 替换插入内容中的附件 URL
            $xml = Utils::replaceAttributes($xml, 'URL', function ($attributes) use ($attachments) {
                if (isset($attributes['title']) && isset($attachments[$attributes['title']])) {
                    $attributes['url'] = $attachments[$attributes['title']];
                }

                return $attributes;
            });

            $post->parsedContent = $xml;
            $post->parseContentHtml = $post->content;

            if(!empty($post->content) && !empty($attachments)){
                $will_parse_content = $post->content;
                $parseContentHtml = preg_replace_callback(
                    '((!\[[^\]]*\])(\((https[^\)]*) ("\d+")\)))',
                    function($m) use ($attachments){
                        if(!empty($m)){
                            $id = trim($m[4], '"');
                            return $m[1].'('.$attachments[$id].' '.$m[4].')';
                        }
                    },
                    $will_parse_content
                );
                $post->parseContentHtml = $parseContentHtml;
            }


        }
    }
}
