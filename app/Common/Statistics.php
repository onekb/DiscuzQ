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

namespace App\Common;


class Statistics
{

    public static function set($key, $value)
    {
        $path = storage_path('statistics') . '/' . sha1($key);
        try {
            $f = fopen($path, 'w');
            fwrite($f, serialize($value));
            fclose($f);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public static function get($key)
    {
        $path = storage_path('statistics') . '/' . sha1($key);
        if (!file_exists($path)) {
            return false;
        }
        $f = fopen($path, 'r');
        $data = fread($f, filesize($path));
        fclose($f);
        return unserialize($data);
    }

    public static function delete($key)
    {
        $path = storage_path('statistics') . '/' . sha1($key);
        if(file_exists($path)){
            return unlink($path);
        }
        return false;
    }
}
