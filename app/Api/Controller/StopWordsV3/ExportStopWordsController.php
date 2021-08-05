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

namespace App\Api\Controller\StopWordsV3;

use App\Common\ResponseCode;
use App\Models\StopWord;
use App\Repositories\UserRepository;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class ExportStopWordsController extends DzqController
{
    use AssertPermissionTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('您没有导出过滤词库的权限');
        }
        return true;
    }

    public function main()
    {
        // 使用 LazyCollection
        $query = StopWord::query();
        $keyword = $this->inPut('keyword');
        if (!empty($keyword)) {
            $query = $query
                ->when($keyword, function ($query, $keyword) {
                    return $query->where('find', 'like', "%$keyword%");
                });
        }
        $stopWords = $query->get()->map(function ($stopWord) {
            if ($stopWord->ugc == '{REPLACE}' && $stopWord->username == 'REPLACE') {
                $replacement = $stopWord->replacement;
            } else {
                $replacement = ($stopWord->ugc == '{REPLACE}' ? $stopWord->replacement : $stopWord->ugc)
                    . '|' . ($stopWord->username == '{REPLACE}' ? $stopWord->replacement : $stopWord->username);
            }

            return $stopWord->find . '=' . $replacement;
        });

        $filename = app()->config('excel.root') . DIRECTORY_SEPARATOR . 'stop-words.txt';

        file_put_contents($filename, '');

        foreach ($stopWords as $stopWord) {
            file_put_contents($filename, $stopWord . "\r\n", FILE_APPEND | LOCK_EX);
        }
        return $this->outPut(ResponseCode::SUCCESS,'', $this->downloadFile($filename));
    }

    protected function downloadFile($filePath, $fileName='', $header = [], $readBuffer = 1024)
    {
        //检测下载文件是否存在 并且可读
        if (!is_file($filePath) && !is_readable($filePath)) {
            return $this->outPut(ResponseCode::RESOURCE_NOT_FOUND,'');
        }

        //判读文件名是否为空
        if (!$fileName){
            $fileName = basename($filePath);
        }
        // dd($header);die;
        //设置头信息
        //声明浏览器输出的是字节流
        $contentType = isset($header['Content-Type']) ? $header['Content-Type'] : 'application/octet-stream';
        header('Content-Type: ' . $contentType);
        //声明浏览器返回大小是按字节进行计算
        header('Accept-Ranges:bytes');
        //告诉浏览器文件的总大小
        $fileSize = filesize($filePath);//坑 filesize 如果超过2G 低版本php会返回负数
        header('Content-Length:' . $fileSize); //注意是'Content-Length:' 非Accept-Length
        $contentDisposition = isset($header['Content-Disposition']) ? $header['Content-Disposition'] : 'attachment;filename=' . $fileName;
        //声明下载文件的名称
        header('Content-Disposition:' . $contentDisposition);//声明作为附件处理和下载后文件的名称
        //获取文件内容
        $handle = fopen($filePath, 'rb');//二进制文件用‘rb’模式读取

        while (!feof($handle) ) { //循环到文件末尾 规定每次读取（向浏览器输出为$readBuffer设置的字节数）
            echo fread($handle, $readBuffer);
        }

        fclose($handle);//关闭文件句柄
        exit;
    }
}
