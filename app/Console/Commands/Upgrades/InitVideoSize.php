<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use App\Models\ThreadVideo;
use App\Repositories\ThreadVideoRepository;
use Discuz\Console\AbstractCommand;
use Discuz\Qcloud\QcloudTrait;

class InitVideoSize extends AbstractCommand
{
    use QcloudTrait;

    protected $signature = 'upgrade:videoSize';

    protected $description = '初始化主题视频的宽高';

    protected $threadVideos;

    public function __construct(ThreadVideoRepository $threadVideos)
    {
        parent::__construct();

        $this->threadVideos = $threadVideos;
    }

    public function handle()
    {
        $threadVideos = $this->threadVideos->query()
            ->where('type', 0)
            ->where('status', 1)
            ->where('height', 0)
            ->where('width', 0)
            ->get();

        $file_ids = $threadVideos->pluck('file_id');
        if ($file_ids->isEmpty()) {
            $this->info('nothing to do!');
        } else {
            $filters = ['metaData'];
            $describeMediaInfos = $this->describeMediaInfos($file_ids, $filters);
            /* @var ThreadVideo $threadVideo*/
            foreach ($threadVideos as $threadVideo) {
                foreach ($describeMediaInfos->MediaInfoSet as $describeMediaInfo) {
                    if ($describeMediaInfo->FileId == $threadVideo->file_id && $describeMediaInfo->MetaData) {
                        $threadVideo->height = $describeMediaInfo->MetaData->Height;
                        $threadVideo->width  = $describeMediaInfo->MetaData->Width;

                        $threadVideo->save();
                    }
                }
            }
            $this->info('success');
        }
    }
}
