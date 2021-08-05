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

namespace App\Api\Controller\SettingsV3;

use App\Common\CacheKey;
use App\Models\Setting;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Base\DzqCache;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Filesystem\Factory;

class DeleteLogoController extends DzqController
{
    public function suffixClearCache($user)
    {
        DzqCache::delKey(CacheKey::SETTINGS);
    }

    /**
     * @var Factory
     */
    protected $filesystem;

    /**
     * @var SettingsRepository
     */
    protected $settings;

    /**
     * 允许删除的类型
     *
     * @var array
     */
    protected $allowTypes = [
        'background_image',
        'watermark_image',
        'header_logo',
        'logo',
        'favicon',
    ];

    /**
     * @param Factory $filesystem
     * @param SettingsRepository $settings
     */
    public function __construct(Factory $filesystem, SettingsRepository $settings)
    {
        $this->filesystem = $filesystem;
        $this->settings = $settings;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $type = $this->inPut('type') ? $this->inPut('type') : 'logo';

        // 类型
        $type = in_array($type, $this->allowTypes) ? $type : 'logo';

        // 设置项 Tag
        $settingTag = $type === 'watermark_image' ? 'watermark' : 'default';

        // 删除原图
        $this->remove($this->settings->get($type, $settingTag));

        // 设置为空
        Setting::modifyValue($type, '', $settingTag);

        return $this->outPut(ResponseCode::SUCCESS,'',[
            'key' => $type,
            'value' => '',
            'tag' => $settingTag
        ]);
    }

    /**
     * @param string $file
     */
    private function remove($file)
    {
        $filesystem = $this->filesystem->disk('public');

        if ($filesystem->has($file)) {
            $filesystem->delete($file);
        }
    }
}
