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

namespace App\Api\Controller\AttachmentV3;

use App\Common\ResponseCode;
use App\Models\Attachment;
use App\Models\Thread;
use App\Repositories\AttachmentRepository;
use App\Repositories\UserRepository;
use App\Settings\SettingsRepository;
use Carbon\Carbon;
use Discuz\Base\DzqController;
use Exception;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class ResourceAttachmentController extends DzqController
{
    private $attachment;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function __construct(AttachmentRepository $attachments, SettingsRepository $settings, Filesystem $filesystem)
    {
        $this->attachments = $attachments;
        $this->settings = $settings;
        $this->filesystem = $filesystem;
    }

    public function main()
    {
        $page = $this->inPut('page');
        $isAttachment = $this->inPut('isAttachment') ? $this->inPut('isAttachment') : 0;

        $user = $this->user;

        $attachment = $this->getAttachment($user);

        if ($attachment->is_remote) {
            $httpClient = new HttpClient();
            $url = $this->filesystem->disk('attachment_cos')->temporaryUrl($attachment->full_path, Carbon::now()->addDay());
            if ($page) {
                $url .= '&ci-process=doc-preview&page='.$page;
            }
            try {
                $response = $httpClient->get($url);
            } catch (Exception $e) {
                if (Str::contains($e->getMessage(), 'FunctionNotEnabled')) {
                    return $this->outPut(ResponseCode::INVALID_PARAMETER,'qcloud_file_preview_unset');
                } else {
                    return $this->outPut(ResponseCode::RESOURCE_NOT_FOUND,'model_not_found');
                }
            }
            if ($response->getStatusCode() == 200) {
                if ($page) {
                    //预览
                    $data = [
                        'X-Total-Page' => $response->getHeader('X-Total-Page')[0],
                        'image' => 'data:image/jpeg;base64,'.base64_encode($response->getBody())
                    ];
                    return $this->outPut(ResponseCode::SUCCESS,'',$data);
                } else {
                    //下载
                    if ($isAttachment) {
                        $header = [
                            'Content-Disposition' => 'attachment;filename=' . $attachment->file_name,
                        ];
                    } else {
                        $header = [
                            'Content-Type' => $attachment->file_type,
                            'Content-Disposition' => 'inline;filename=' . $attachment->file_name,
                        ];
                    }
                    $this->downloadFile($response->getBody(),$attachment->file_name,$header);
                }
            } else {
                return $this->outPut(ResponseCode::RESOURCE_NOT_FOUND,'model_not_found');
            }
        } else {
            $filePath = storage_path('app/' . $attachment->full_path);

            // 帖子图片直接显示
            if ($attachment->type == Attachment::TYPE_OF_IMAGE) {
                // 是否要获取缩略图
                if (Arr::get($this->request->getQueryParams(), 'thumb') === 'true') {
                    $thumb = Str::replaceLast('.', '_thumb.', $filePath);

                    // 缩略图是否存在
                    if (! file_exists($thumb)) {
                        $img = (new ImageManager())->make($filePath);

                        $img->resize(Attachment::FIX_WIDTH, Attachment::FIX_WIDTH, function ($constraint) {
                            $constraint->aspectRatio();     // 保持纵横比
                            $constraint->upsize();          // 避免文件变大
                        })->save($thumb);
                    }

                    $filePath = $thumb;
                }
                $this->downloadFile($filePath);
            }
            $this->downloadFile($filePath,$attachment->file_name);
        }
    }

    protected function getAttachment($actor)
    {
        $attachment = $this->attachment;

        $post = $attachment->post;

        Thread::setStateUser($actor);

        $thread = $post->thread;

        // 主题是否收费
        if ($thread->price > 0 && ! $thread->is_paid) {
            return $this->outPut(ResponseCode::RESOURCE_NOT_FOUND,trans('order.order_post_not_found'));
        }

        // 主题附件是否付费
        if ($thread->attachment_price > 0 && ! $thread->is_paid_attachment) {
            return $this->outPut(ResponseCode::RESOURCE_NOT_FOUND,trans('order.order_post_not_found'));
        }

        return $attachment;
    }

    protected function downloadFile($filePath, $fileName='', $header = [], $readBuffer = 1024, $allowExt = ['jpeg', 'jpg', 'peg', 'gif', 'zip'])
    {
        //检测下载文件是否存在 并且可读
        if (!is_file($filePath) && !is_readable($filePath)) {
            return $this->outPut(ResponseCode::RESOURCE_NOT_FOUND,'');
        }
        //检测文件类型是否允许下载
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowExt)) {
            return $this->outPut(ResponseCode::UNAUTHORIZED,'');
        }
        //判读文件名是否为空
        if (!$fileName){
            $fileName = basename($filePath);
        }
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
