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

use App\Common\ResponseCode;
use App\Models\Setting;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class ListSettingsController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $key = $this->inPut('key');
        $tag = $this->inPut('tag');
        $this->outPut(ResponseCode::SUCCESS, '', Setting::where([['key', $key], ['tag', $tag]])->get());
    }
}
