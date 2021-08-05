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

namespace App\Models;

use App\Common\CacheKey;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqModel;

/**
 * @property string $category
 * @property string $url
 * @property string $code
 * @property int $order
 * @method truncate()
 * @method create(array $array)
 * @package App\Models
 */
class Emoji extends DzqModel
{

    public static function getEmojis()
    {
        $cache = app('cache');
        $emojis = $cache->get(CacheKey::LIST_EMOJI);
        if ($emojis) {
            return $emojis;
        }
        $emojis = Emoji::all()->toArray();
        $cache->put(CacheKey::LIST_EMOJI, $emojis, 60 * 60);
        return $emojis;
    }

    public static function getEmojiListForController($request)
    {
        $emojis = Emoji::getEmojis();
        $url = $request->getUri();
        $port = $url->getPort();
        $port = $port == null ? '' : ':' . $port;
        $path = $url->getScheme() . '://' . $url->getHost() . $port . '/';
        foreach ($emojis as $k => $v) {
            $emojis[$k]['url'] = $path . $v['url'];
        }
        return $emojis;
    }

    protected function clearCache()
    {
        DzqCache::delKey(CacheKey::LIST_EMOJI);
    }
}
