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
        return true;
    }

    public function main()
    {
        $data = [
            'sign' => $this->inPut('sign'),
            'attachmentsId' => $this->inPut('attachmentsId')
        ];

        $this->dzqValidate($data, [
            'sign' => 'required',
            'attachmentsId' => 'required|int',
        ]);

        $share = AttachmentShare::query()
            ->where(['sign' => $data['sign'], 'attachments_id' => $data['attachmentsId']])
            ->first();

        if (empty($share) || strtotime($share->expired_at) < time()) {
            app('log')->info("requestId：{$this->requestId},分享记录不存在，时间已过期");
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }

        $attachment = Attachment::query()->where('id', $data['attachmentsId'])->first();
        if (empty($attachment)) {
            app('log')->info("requestId：{$this->requestId},附件不存在");
            $this->outPut(ResponseCode::RESOURCE_NOT_FOUND);
        }

        if ($attachment->is_remote) {
            $url = $this->settings->get('qcloud_cos_sign_url', 'qcloud', true)
                ? $this->filesystem->disk('attachment_cos')->temporaryUrl($attachment->full_path, Carbon::now()->addDay())
                : $this->filesystem->disk('attachment_cos')->url($attachment->full_path);
        } else {
            $url = $this->filesystem->disk('attachment')->url($attachment->full_path);
        }

        AttachmentShare::query()->where('sign', $data['sign'])->update([
            'download_count' => intval($share->download_count + 1),
            'updated_at' => Carbon::now()
        ]);
        $origin_name = iconv("utf-8", "gb2312", $attachment->file_name);
        header("Content-type:application/octet-stream");
        header("Content-Disposition:attachment;filename = " . $origin_name);
        header("Accept-ranges:bytes");
        header("Accept-length:" . $attachment->file_size);
        readfile($url, false, stream_context_create(['ssl'=>['verify_peer'=>false, 'verify_peer_name'=>false]]));
        exit;
    }
}
