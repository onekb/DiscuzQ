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

namespace App\Console\Commands\Upgrades;

use Illuminate\Support\Collection;

trait NoticeTrait
{
    /**
     * 检查是否是老数据
     * (未统一 type_name 之前的数据)
     *
     * @param Collection $tplCollect
     * @return bool
     */
    protected function checkIterationBeforeData(Collection $tplCollect) : bool
    {
        $first = $tplCollect->first();
        if ($first->id == 1 && $first->type_name == '新用户注册并加入后') {
            return true;
        }

        return false;
    }
}
