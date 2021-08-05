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
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Vod\V20180717\Models\ProcessMediaRequest;
use TencentCloud\Vod\V20180717\VodClient;


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

    protected $url = 'vod.tencentcloudapi.com';

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
            ->select('tv.*')
            ->from('thread_video as tv')
            ->join('threads as th', 'th.id', '=', 'tv.thread_id')
            ->whereNull('th.deleted_at')
            ->whereNotNull('th.user_id')
            ->where('th.is_draft', Thread::IS_NOT_DRAFT)
            ->where('th.is_display', Thread::BOOL_YES)
            ->where('tv.status', ThreadVideo::VIDEO_STATUS_TRANSCODING)
            ->where('tv.type', ThreadVideo::TYPE_OF_VIDEO)
            ->where('tv.thread_id','!=',0)
            ->where('tv.updated_at', '<', Carbon::now()->subMinute(5)->toDateTimeString())
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
            $log = app('log');
            $threadVideos->map(function ($item) use ($settingRepo,$newThreadVideos,$log) {
                try {
                    if (!empty($newThreadVideos[$item->id])) {
                        //转码
                        $resTranscode = $this->transcodeVideo($item->file_id, 'TranscodeTaskSet');

                        // 转动图
                        if ($template_name = $settingRepo->get('qcloud_vod_taskflow_gif', 'qcloud')) {
                            $this->processMediaByProcedure($item->file_id, $template_name);
                        }
                        if($resTranscode){
                            $item->status = ThreadVideo::VIDEO_STATUS_SUCCESS;
                            $item->save();
                            $log->info('普通转码成功,videoId为'.$item->id.",taskId为".$resTranscode->TaskId);
                        }
                    }
                } catch (Exception $e) {
                    if (!empty($newThreadVideos[$item->id])) {
                        $fileId = $item->file_id;
                        $resp = $this->processMedia($fileId,$settingRepo);
                        if(empty($resp['TaskId'])){
                            $log->info('转码任务未执行,videoId为'.$item->id);
                            return;
                        }
                        $item->status = ThreadVideo::VIDEO_STATUS_SUCCESS;
                        $item->save();
                        $log->info('sdk上传视频转码成功,videoId为'.$item->id.",taskId为".$resp['TaskId']);
                    }
                }
            });
        }
        $this->info('转码脚本执行 [结束]');
    }

    //兼容sdk上传视频转码
    public function ProcessMedia($fileId,$settingRepo){
        $secretId = $settingRepo->get('qcloud_secret_id', 'qcloud');
        $secretKey = $settingRepo->get('qcloud_secret_key', 'qcloud');

        $cred = new Credential($secretId, $secretKey);
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint($this->url);

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new VodClient($cred, "", $clientProfile);
        $req = new ProcessMediaRequest();
        $params = array(
            'FileId'=>$fileId
        );

        $req->fromJsonString(json_encode($params));
        $resp = $client->ProcessMedia($req);
        $resp = json_decode($resp->toJsonString(),true);
        return $resp;
    }
}
