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

namespace App\Api\Controller\EmojiV3;

use App\Common\ResponseCode;
use App\Models\Emoji;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class ListEmojiController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $emojis = Emoji::getEmojiListForController($this->request);
        $result = $this->camelData($emojis);
        $this->outPut(ResponseCode::SUCCESS, '', $result);
    }
}
