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

namespace App\Api\Serializer;

use Carbon\Carbon;
use Discuz\Api\Serializer\AbstractSerializer;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Tobscure\JsonApi\Relationship;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;

class DialogMessageSerializer extends AbstractSerializer
{
    protected $type = 'dialog_message';

    protected $settings;
    protected $filesystem;
    protected $url;

    public function __construct(Filesystem $filesystem, SettingsRepository $settings, UrlGenerator $url)
    {
        $this->filesystem = $filesystem;
        $this->settings = $settings;
        $this->url = $url;
    }

    public function getDefaultAttributes($model)
    {
        $image_url = '';
        if($model->attachment_id){
            $attachment = $model->attachment;
            if ($attachment->is_remote) {
                $image_url = $this->settings->get('qcloud_cos_sign_url', 'qcloud', true)
                    ? $this->filesystem->disk('attachment_cos')->temporaryUrl($attachment->full_path, Carbon::now()->addDay())
                    : $this->filesystem->disk('attachment_cos')->url($attachment->full_path);
            } else {
                $image_url = $this->filesystem->disk('attachment')->url($attachment->full_path);
            }
        }

        return [
            'user_id' => $model->user_id,
            'dialog_id' => $model->dialog_id,
            'attachment_id' => $model->attachment_id,
            'summary' => $model->summary,
            'message_text' => $model->getMessageText(),
            'message_text_html'  => $model->formatMessageText(),
            'image_url'  => $image_url ?: $model->getImageUrlMessageText(),
            'updated_at' => $this->formatDate($model->updated_at),
            'created_at' => $this->formatDate($model->created_at)
        ];
    }

    /**
     * User
     * @param $model
     * @return Relationship
     */
    public function user($model)
    {
        return $this->hasOne($model, UserSerializer::class);
    }

    /**
     * Attachment
     * @param $model
     * @return Relationship
     */
    public function attachment($model)
    {
        return $this->hasOne($model, AttachmentSerializer::class);
    }
}
