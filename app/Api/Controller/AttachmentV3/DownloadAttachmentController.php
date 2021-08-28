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
use App\Common\Utils;
use App\Models\Attachment;
use App\Models\AttachmentShare;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Discuz\Base\DzqController;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;

class DownloadAttachmentController extends DzqController
{

    protected $filesystem;

    protected $settings;

    public function __construct(Filesystem $filesystem, SettingsRepository $settings)
    {
        $this->filesystem = $filesystem;
        $this->settings = $settings;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $user = $this->user;
        $data = [
            'sign' => $this->inPut('sign'),
            'attachmentsId' => $this->inPut('attachmentsId'),
            'isCode' => $this->inPut('isCode')
        ];
        $this->dzqValidate($data, [
            'sign' => 'required',
            'attachmentsId' => 'required|int',
        ]);
        $attachment = Attachment::query()->where('id', $data['attachmentsId'])->first();
        $share = AttachmentShare::query()
            ->where(['sign' => $data['sign'], 'attachments_id' => $data['attachmentsId']])
            ->first();
        if(isset($data['isCode']) && !empty($data['isCode'])){
            if (empty($attachment)) {
                app('log')->info("requestId：{$this->requestId},附件不存在");
                return $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
            }
            $share = AttachmentShare::query()
                ->where(['sign' => $data['sign'], 'attachments_id' => $data['attachmentsId']])
                ->first();
            if (empty($share)) {
                app('log')->info("requestId：{$this->requestId},分享记录不存在");
                return $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
            }
            if(strtotime($share->expired_at) < time()){
                app('log')->info("requestId：{$this->requestId},下载资源已失效");
                return $this->outPut(ResponseCode::DOWNLOAD_RESOURCE_IS_INVALID);
            }
            //限制下载次数
            $downloadNum = (int)$this->settings->get('support_max_download_num', 'default');
            $todayTime = Utils::getTodayTime();
            $attachmentDownloaded = AttachmentShare::query()
                ->where('user_id',$user->id)
                ->where('attachments_id',$data['attachmentsId'])
                ->where('download_count','>=',1)
                ->whereBetween('updated_at', array($todayTime['begin'], $todayTime['end']))->get()->toArray();
            //针对用户当天没下载过的附件进行限制判断
            if($downloadNum > 0 && !$attachmentDownloaded){
                $dayLimitCount = AttachmentShare::query()
                    ->where('user_id',$user->id)
                    ->where('download_count','>=',1)
                    ->whereBetween('updated_at', array($todayTime['begin'], $todayTime['end']))
                    ->count('sign');
                if((int)$dayLimitCount >= $downloadNum){
                    app('log')->info("requestId：{$this->requestId},超过今天可下载附件的最大次数");
                    return $this->outPut(ResponseCode::DOWNLOAD_NUMS_IS_TOPLIMIT);
                }
            }
            return $this->outPut(ResponseCode::SUCCESS);
        }
        if ($attachment->is_remote) {
            $url = $this->settings->get('qcloud_cos_sign_url', 'qcloud', true)
                ? $this->filesystem->disk('attachment_cos')->temporaryUrl($attachment->full_path, Carbon::now()->addDay())
                : $this->filesystem->disk('attachment_cos')->url($attachment->full_path);
        } else {
            $url = $this->filesystem->disk('attachment')->url($attachment->full_path);
        }

        $today = Utils::getTodayTime();
        $dayUserCount = AttachmentShare::query()
            ->where('attachments_id',$data['attachmentsId'])
            ->where('user_id',$user->id)
            ->whereBetween('updated_at', array($today['begin'], $today['end']))
            ->sum('download_count');
        if((int)$dayUserCount == 0){
            AttachmentShare::query()->where('sign', $data['sign'])->update([
                'download_count' => intval($share->download_count + 1),
                'updated_at' => Carbon::now()
            ]);
        }
        $origin_name = iconv("utf-8", "gb2312", $attachment->file_name);
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:*');
        header("Content-type:application/octet-stream");
        header("Content-Disposition:attachment;filename = " . $origin_name);
        header("Accept-ranges:bytes");
        header("Accept-length:" . $attachment->file_size);
        readfile($url, false, stream_context_create(['ssl'=>['verify_peer'=>false, 'verify_peer_name'=>false]]));
        exit;
    }
}
