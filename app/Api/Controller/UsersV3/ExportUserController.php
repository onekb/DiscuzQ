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

namespace App\Api\Controller\UsersV3;

use App\Common\ResponseCode;
use App\Exports\UsersExport;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Traits\UserTrait;
use Discuz\Base\DzqController;
use Discuz\Foundation\Application;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;

class ExportUserController extends DzqController
{
    use UserTrait;

    protected $bus;

    protected $app;

    public function __construct(BusDispatcher $bus, Application $app)
    {
        $this->bus = $bus;
        $this->app = $app;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $userRepo->canExportUser($this->user);
    }

    public function main()
    {
        $filter = $this->inPut('filter');
        $filters = $filter ?: [];
        $ids = $this->inPut('ids','');
        $filters['id'] = $ids;
        $data= $this->ExportFilter($filters);

        $filename = $this->app->config('excel.root') . DIRECTORY_SEPARATOR . 'user_excel.xlsx';
        //TODO 判断满足条件的excel是否存在,if exist 直接返回;
        $this->bus->dispatch(
            new UsersExport($filename, $data)
        );
        
        return $this->outPut(ResponseCode::SUCCESS,'', $this->downloadFile($filename));
    }


    public function ExportFilter($filters)
    {
        $userField = [
            'id',
            'username',
            'mobile',
            'login_at',
            'last_login_ip',
            'last_login_port',
            'register_ip',
            'register_port',
            'users.status',
            'users.created_at',
            'users.updated_at',
        ];
        $wechatField = [
            'user_id',
            'nickname',
            'sex',
            'mp_openid',
            'unionid',
        ];

        $columnMap = [
            'id',
            'username',
            'originalMobile',
            'status',
            'sex',
            'groups',
            'mp_openid',
            'unionid',
            'nickname',
            'created_at',
            'register_ip',
            'register_port',
            'login_at',
            'last_login_ip',
            'last_login_port',
        ];

        $query = User::query();

        // 拼接条件
        $this->applyFilters($query, $filters);

        $users = $query->with(['wechat' => function ($query) use ($wechatField) {
            $query->select($wechatField);
        }, 'groups' => function ($query) {
            $query->select(['id', 'user_id', 'name']);
        }])->get($userField);

        $sex = ['', '男', '女'];
        return $users->map(function (User $user) use ($columnMap, $sex) {
            // 前面加空格，避免科学计数法
            $user->originalMobile = ' ' . $user->getRawOriginal('mobile');
            $user->sex = $sex[$user->wechat ? $user->wechat->sex : 0];
            $user->status = User::$statusMap[$user->status] ?? '';
            if (!is_null($user->groups)) {
                $user->groups = $user->groups->pluck('name')->implode(',');
            }
            if (!is_null($user->wechat)) {
                $user->nickname = $user->wechat->nickname;
                $user->mp_openid = $user->wechat->mp_openid;
                $user->unionid = $user->wechat->unionid;
            }
            $user->unsetRelation('wechat');
            $user->unsetRelation('groups');
            return $user->only($columnMap);
        })->toArray();
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
