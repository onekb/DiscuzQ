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

namespace App\Api\Controller\Settings;

use App\Api\Serializer\ForumSettingSerializerV2;
use App\Common\ResponseCode;
use App\Models\User;
use App\Settings\SettingsRepository;
use Discuz\Base\DzqController;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ForumSettingsV2Controller extends DzqController
{

    public $settings;

    public function __construct(SettingsRepository $settings)
    {
        $this->settings = $settings;
    }

    public function main()
    {
        $forum_serialize = $this->app->make(ForumSettingSerializerV2::class);
        $data = $forum_serialize->getDefaultAttributes($this->user);

        $tag = Str::of($this->inPut('tag'))->replace(' ', '')->explode(',')->filter();
        if($tag->contains('agreement')){
            $agreement = $this->settings->tag('agreement') ?? [];
            $data['agreement'] = [
                'privacy' => (bool) ($agreement['privacy'] ?? false),
                'privacy_content' => $agreement['privacy_content'] ?? '',
                'register' => (bool) ($agreement['register'] ?? false),
                'register_content' => $agreement['register_content'] ?? '',
            ];
        }

        $data = $this->camelData($data);

        return $this->outPut(ResponseCode::SUCCESS,'', $data);

    }

}