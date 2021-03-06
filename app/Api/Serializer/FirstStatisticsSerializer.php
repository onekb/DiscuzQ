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

use Discuz\Api\Serializer\AbstractSerializer;
use Illuminate\Support\Arr;

class FirstStatisticsSerializer extends AbstractSerializer
{
    protected $type = 'first_statistic';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param object|array $model
     * @return array
     */
    public function getDefaultAttributes($model)
    {
        return [
            'threadData'           => Arr::get($model, 'threadData', []),
            'postData'             => Arr::get($model, 'postData', []),
            'activeUserData'       => Arr::get($model, 'activeUserData', []),
            'joinUserData'       => Arr::get($model, 'joinUserData', []),
            'overData'       => Arr::get($model, 'overData', []),
        ];
    }

    public function getId($model)
    {
        return 1;
    }
}
