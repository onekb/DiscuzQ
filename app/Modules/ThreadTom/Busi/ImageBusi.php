<?php
/**
 * Copyright (C) 2021 Tencent Cloud.
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

namespace App\Modules\ThreadTom\Busi;

use App\Api\Serializer\AttachmentSerializer;
use App\Common\CacheKey;
use Discuz\Base\DzqCache;
use App\Common\ResponseCode;
use App\Models\Attachment;
use App\Models\Thread;
use App\Modules\ThreadTom\TomBaseBusi;

class ImageBusi extends TomBaseBusi
{
    public function create()
    {
        $imageIds = $this->getParams('imageIds');
        if (count($imageIds) > 9) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '图片数量不能超过9张');
        }
        app('log')->info('帖子id'.$this->threadId.'图片入参：' . json_encode($imageIds));
        return $this->jsonReturn(['imageIds' => $imageIds]);
    }

    public function update()
    {
        $imageIds = $this->getParams('imageIds');
        if (count($imageIds) > 9) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '图片数量不能超过9张');
        }
        return $this->jsonReturn(['imageIds' => $imageIds]);
    }

    public function select()
    {
        $serializer = $this->app->make(AttachmentSerializer::class);
        $result = [];
        $imageIds = $this->getParams('imageIds');
        $attachments = DzqCache::hMGetCollection(CacheKey::LIST_THREADS_V3_ATTACHMENT, $imageIds, function ($imageIds) {
            return Attachment::query()->whereIn('id', $imageIds)->get()->keyBy('id');
        });
        $threadId = $this->threadId;
        $thread = DzqCache::hGet(CacheKey::LIST_THREADS_V3_THREADS, $threadId, function ($threadId) {
            return Thread::getOneThread($threadId, true);
        });

        foreach ($attachments as $attachment) {
            if (!empty($thread)) {
                $item = $this->camelData($serializer->getBeautyAttachment($attachment, $thread, $this->user));
                if (!$this->canViewTom) {
                    $item['url'] = $item['thumbUrl'] = $item['blurUrl'];
                }
                unset($item['blurUrl']);
                $result[] = $item;
            }
        }
        return $this->jsonReturn($result);
    }
}
