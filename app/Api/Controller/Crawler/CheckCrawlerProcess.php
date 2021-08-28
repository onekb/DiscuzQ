<?php


namespace App\Api\Controller\Crawler;

use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class CheckCrawlerProcess extends DzqController
{
    use CrawlerTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $publicPath = public_path();
        $lockPath = $publicPath . DIRECTORY_SEPARATOR . 'crawlerSplQueueLock.conf';
        $lockFileContent = $this->getLockFileContent($lockPath);
        $this->outPut(ResponseCode::SUCCESS, '', $lockFileContent);
    }
}