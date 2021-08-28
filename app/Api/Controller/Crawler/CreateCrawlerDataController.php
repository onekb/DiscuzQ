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

namespace App\Api\Controller\Crawler;

use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\Category;
use App\Models\Thread;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class CreateCrawlerDataController extends DzqController
{
    use CrawlerTrait;

    private $crawlerPlatform;

    private $categoryId;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $topic = $this->input('topic');
        if (empty($topic)) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '请输入话题！');
        }
        $category = Category::query()->select('id')->orderBy('id', 'asc')->first()->toArray();
        if (empty($category)) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '缺少分类，请您先创建内容分类！');
        }

        $this->categoryId = $category['id'];
        $this->crawlerPlatform = $this->input('platform') ?: Thread::CRAWLER_DATA_PLATFORM_OF_WEIBO;
        $number = $this->input('number');
        if ($number <= 0 || $number > 1000) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '请输入正确的导入条数！');
        }

        $publicPath = public_path();
        $lockPath = $publicPath . DIRECTORY_SEPARATOR . 'crawlerSplQueueLock.conf';
        if (file_exists($lockPath)) {
            $lockFileContent = $this->getLockFileContent($lockPath);
            if ($lockFileContent['runtime'] < Thread::CREATE_CRAWLER_DATA_LIMIT_MINUTE_TIME && $lockFileContent['status'] == Thread::IMPORT_PROCESSING) {
                $this->outPut(ResponseCode::RESOURCE_IN_USE, '当前正在导入内容，请勿重复操作！当前已执行' . $lockFileContent['runtime'] . '分钟。');
            } else if ($lockFileContent['runtime'] > Thread::CREATE_CRAWLER_DATA_LIMIT_MINUTE_TIME) {
                app('cache')->clear();
                $this->changeLockFileContent($lockPath, 0, Thread::PROCESS_OF_START_INSERT_CRAWLER_DATA, Thread::IMPORT_TIMEOUT_ENDING);
            }
        }

        $inputData = [
            'topic'    => $topic,
            'platform' => $this->crawlerPlatform,
            'number'   => $number,
            'categoryId' => $this->categoryId
        ];

        $crawlerSplQueue = new \SplQueue();
        $crawlerSplQueue->enqueue($inputData);
        app('cache')->put(CacheKey::CRAWLER_SPLQUEUE_INPUT_DATA, $crawlerSplQueue);
        $this->outPut(ResponseCode::SUCCESS, '内容导入开始！');
    }
}
