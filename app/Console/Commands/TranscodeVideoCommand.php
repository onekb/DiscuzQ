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

namespace App\Console\Commands;

use App\Models\Thread;
use App\Models\ThreadVideo;
use App\Repositories\ThreadVideoRepository;
use App\Settings\SettingsRepository;
use Discuz\Console\AbstractCommand;
use Discuz\Foundation\Application;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\ConnectionInterface;
use Discuz\Qcloud\QcloudStatisticsTrait;

class TranscodeVideoCommand extends AbstractCommand
{
    use QcloudStatisticsTrait;

    protected $signature = 'transcode:update';

    protected $description = '视频转码';

    protected $app;

    /**
     * @var ConnectionInterface
     */
    protected $threadVideo;

    /**
     * AvatarCleanCommand constructor.
     * @param string|null $name
     * @param Application $app
     * @param ConnectionInterface $connection
     */
    public function __construct(string $name = null, Application $app, ThreadVideoRepository $threadVideo)
    {
        parent::__construct($name);

        $this->app = $app;
        $this->threadVideo = $threadVideo;
    }

    public function handle()
    {
        $this->info('转码脚本执行 [开始]');
        $this->info('');

        //获取超过5分钟还没成功回调的视频
        $threadVideos = ThreadVideo::query()
            ->where('status', ThreadVideo::VIDEO_STATUS_TRANSCODING)
            ->where('type', ThreadVideo::TYPE_OF_VIDEO)
            ->where('updated_at', '<', Carbon::now()->subMinute(5)->toDateTimeString())
            ->get();

        $threadVideosArr = $threadVideos->toArray();

        $threadIds = array_unique(array_column($threadVideosArr, 'thread_id'));
        $threads = Thread::query()->whereIn('id', $threadIds)->get()->keyBy("id")->toArray();
        $newThreadVideos = [];
        foreach ($threadVideosArr as $k=>$val){
            $newThreadVideos[$val['id']] = $threads[$val['thread_id']];
        }

        if($threadVideos){
            $settingRepo = app(SettingsRepository::class);
            $threadVideos->map(function ($item) use ($settingRepo,$newThreadVideos) {
                try {
                    if (!empty($newThreadVideos[$item->id])  && empty($newThreadVideos[$item->id]['is_draft'])) {
                        //转码
                        $resTranscode = $this->transcodeVideo($item->file_id, 'TranscodeTaskSet');

                        // 转动图
                        if ($template_name = $settingRepo->get('qcloud_vod_taskflow_gif', 'qcloud')) {
                            $this->processMediaByProcedure($item->file_id, $template_name);
                        }
                        if($resTranscode){
                            $item->status = ThreadVideo::VIDEO_STATUS_SUCCESS;
                            $item->save();
                        }
                    }
                } catch (Exception $e) {
                    app('log')->info('转码失败,videoId:'.$item->id);
                }
            });
        }
        $this->info('转码脚本执行 [结束]');

    }
}
