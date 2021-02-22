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

namespace App\Common;

class SettingCache
{
    const BLUE_SKIN_CODE = 1;

    const RED_SKIN_CODE = 2;

    public function getSiteSkin()
    {
        $public_path = public_path();
        $site_skin = 1;
        if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
            if(is_dir($public_path)){
                if($dh = opendir($public_path)){
                    $skin_file = 'skin.conf';
                }
            }
        }else{
            $skin_file = $public_path . DIRECTORY_SEPARATOR .'skin.conf';
        }

        if(file_exists($skin_file)){
            $site_skin = file_get_contents($skin_file);
        }

        return $site_skin;
    }
}
