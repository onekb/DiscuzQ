<?php


namespace App\Api\Controller\Crawler;

use App\Models\Thread;

trait CrawlerTrait
{
    public function getLockFileContent($lockPath)
    {
        if (!file_exists($lockPath)) {
            $lockFileContent = $this->changeLockFileContent($lockPath, 0, 0, Thread::IMPORT_WAITING);
        } else {
            $lockFileContent = file_get_contents($lockPath);
            $lockFileContent = json_decode($lockFileContent, true);
            $lockFileContent['runtime'] = $lockFileContent['startCrawlerTime'] ? floor((time() - strtotime($lockFileContent['startCrawlerTime']))%86400/60) : 0;
        }

        return $lockFileContent;
    }

    public function changeLockFileContent($lockPath, $startCrawlerTime, $progress, $status)
    {
        if (!file_exists($lockPath)) {
            touch($lockPath);
        }
        $data = [
            'status' => $status, // 0 未开始;1 进行中;2 正常结束;3 异常结束;4 超时
            'progress' => floor((string)$progress),
            'startCrawlerTime' => $startCrawlerTime,
            'runtime' => 0
        ];
        $writeCrawlerSplQueueLock = fopen($lockPath, 'w');
        fwrite($writeCrawlerSplQueueLock, json_encode($data));
        return $data;
    }
}