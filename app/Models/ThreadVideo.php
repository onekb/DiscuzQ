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

namespace App\Models;

use App\Common\CacheKey;
use App\Settings\SettingsRepository;
use Carbon\Carbon;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $thread_id
 * @property int $post_id
 * @property int $user_id
 * @property int $type
 * @property int $status
 * @property string $reason
 * @property string $file_name
 * @property string $file_id
 * @property int $width
 * @property int $height
 * @property float $duration
 * @property string $media_url
 * @property string $cover_url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Thread $thread
 * @property Post $post
 */
class ThreadVideo extends DzqModel
{
    const TYPE_OF_VIDEO = 0; // 视频

    const TYPE_OF_AUDIO = 1; // 音频

    const VIDEO_STATUS_TRANSCODING = 0; // 转码中

    const VIDEO_STATUS_SUCCESS = 1;     // 转码完成

    const VIDEO_STATUS_FAIL = 2;        // 转码失败


    private $typeDic = [
        self::TYPE_OF_AUDIO => '音频',
        self::TYPE_OF_VIDEO => '视频'
    ];
    /**
     * {@inheritdoc}
     */
    protected $table = 'thread_video';

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * Define the relationship with the video's thread.
     *
     * @return BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Define the relationship with the audio's post.
     *
     * @return BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function getThreadVideo($threadId, $type = null, $isV3 = false)
    {
        $video = self::query()->where(['thread_id' => $threadId, 'status' => self::VIDEO_STATUS_SUCCESS]);
        if ($type != null) {
            $video = $video->where('type', $type);
        }
        $video = $video->first();
        if (empty($video)) {
            return false;
        }
        $result = [
            'fileName' => $video['file_name'],
            'height' => $video['height'],
            'width' => $video['width'],
            'duration' => $video['duration'],
            'status' => $video['status'],
            'mediaUrl' => $this->getMediaUrl($video),
            'coverUrl' => $video['cover_url']
        ];
        if (!$isV3) {
            $result ['pid'] = $video['id'];
        } else {
            $result ['id'] = $video['id'];
        }
        return $result;
    }

    /**
     * @desc v3接口调用
     * @param $videoId
     * @param null $video
     * @return array|bool
     */
    public function getThreadVideoById($videoId, $video = null)
    {
        if (is_null($video)) {
            $video = self::query()->where(['id' => $videoId, 'status' => self::VIDEO_STATUS_SUCCESS])->first();
        }
        if (empty($video)) {
            return false;
        }
        return [
            'threadVideoId' => $video['id'],
            'fileName' => $video['file_name'],
            'height' => $video['height'],
            'width' => $video['width'],
            'duration' => $video['duration'],
            'status' => $video['status'],
            'mediaUrl' => $this->getMediaUrl($video),
            'coverUrl' => $video['cover_url']
        ];
    }

    public function threadVideoResult($threadVideo)
    {
        if (empty($threadVideo)) {
            return false;
        }
        return [
            'threadVideoId' => $threadVideo['id'],
            'fileName' => $threadVideo['file_name'],
            'height' => $threadVideo['height'],
            'width' => $threadVideo['width'],
            'duration' => $threadVideo['duration'],
            'mediaUrl' => $this->getMediaUrl($threadVideo),
            'status' => $threadVideo['status'],
            'coverUrl' => $threadVideo['cover_url']
        ];
    }

    public function getMediaUrl($video)
    {
        $settings = app(SettingsRepository::class);
        $mediaUrl = $video['media_url'];
        $urlKey = $settings->get('qcloud_vod_url_key', 'qcloud');
        $urlExpire = (int)$settings->get('qcloud_vod_url_expire', 'qcloud');
        if ($urlKey  && !empty($mediaUrl)) {
            $currentTime = Carbon::now()->timestamp;
            $dir = Str::beforeLast(parse_url($mediaUrl)['path'], '/') . '/';
            $t = dechex($currentTime + $urlExpire);
            $us = Str::random(10);
            $sign = md5($urlKey . $dir . $t . $us);
            $mediaUrl = $mediaUrl . '?t=' . $t . '&us=' . $us . '&sign=' . $sign;
        }
        return $mediaUrl;
    }

    protected function clearCache()
    {
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_VIDEO, $this->id);
    }
}
