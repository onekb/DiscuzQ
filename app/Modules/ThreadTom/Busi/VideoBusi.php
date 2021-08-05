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


use App\Common\CacheKey;
use Discuz\Base\DzqCache;
use App\Models\Thread;
use App\Models\ThreadVideo;
use App\Modules\ThreadTom\TomBaseBusi;
use App\Models\Setting;
use Discuz\Qcloud\QcloudTrait;

class VideoBusi extends TomBaseBusi
{

    use QcloudTrait;

    public function create()
    {
        $videoId = $this->getParams('videoId');
        $video = ThreadVideo::query()->where('id', $videoId)->first();
        if (!empty($video) && !empty($this->threadId)) {
            $video->thread_id = $this->threadId;

            if ($video->type === ThreadVideo::TYPE_OF_VIDEO) {
                $video->status = ThreadVideo::VIDEO_STATUS_TRANSCODING;
            } else {
                $video->status = ThreadVideo::VIDEO_STATUS_SUCCESS;
            }
            $video->save();

            $thread = Thread::query()->where('id', $this->threadId)->first();
            if ($video->type == ThreadVideo::TYPE_OF_VIDEO && $thread && $thread['is_draft'] == 0) {
                // 发布文章时，转码
                $this->transcodeVideo($video->file_id, 'TranscodeTaskSet');
                // 转动图
                $taskflow = Setting::query()->where('key', 'qcloud_vod_taskflow_gif')->where('tag', 'qcloud')->first();
                if ($taskflow && $taskflow['value']) {
                    // 转动图
                    $this->processMediaByProcedure($video->file_id, $taskflow['value']);

                }
            }
        }
        return $this->jsonReturn(['videoId' => $videoId]);
    }

    public function update()
    {
        $videoId = $this->getParams('videoId');
        $video = ThreadVideo::query()->where('id', $videoId)->first();
        if (!empty($video) && !empty($this->threadId)) {
            $video->thread_id = $this->threadId;
            $video->save();
            $thread = Thread::query()->where('id', $this->threadId)->first();
            if ($video->type == ThreadVideo::TYPE_OF_VIDEO && $thread && $thread['is_draft'] == 0 && $video->status == ThreadVideo::VIDEO_STATUS_TRANSCODING) {
                // 发布文章时，转码
                $this->transcodeVideo($video->file_id, 'TranscodeTaskSet');
                // 转动图
                $taskflow = Setting::query()->where('key', 'qcloud_vod_taskflow_gif')->where('tag', 'qcloud')->first();
                if ($taskflow && $taskflow['value']) {
                    // 转动图
                    $this->processMediaByProcedure($video->file_id, $taskflow['value']);
                }
            }
        }
        return $this->jsonReturn(['videoId' => $videoId]);
    }

    public function select()
    {
        $videoId = $this->getParams('videoId');
        $video = DzqCache::hGet(CacheKey::LIST_THREADS_V3_VIDEO, $videoId, function ($videoId) {
            $videos = ThreadVideo::query()->where(['id' => $videoId])->get()->toArray();
            if (empty($videos)) {
                $video = null;
            } else {
                $video = current($videos);
            }
            return $video;
        });
        if ($video) {
            $video = ThreadVideo::instance()->threadVideoResult($video);
            if (!$this->canViewTom) {
                $video['mediaUrl'] = '';
            }
        } else {
            $video = false;
        }
        return $this->jsonReturn($video);
    }
}
