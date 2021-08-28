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

namespace App\Modules\ThreadTom\Busi;


use App\Modules\ThreadTom\TomBaseBusi;

class DefaultBusi extends TomBaseBusi
{
    public function create()
    {
        return $this->jsonReturn($this->body);
    }

    public function update()
    {
        return $this->jsonReturn($this->body);
    }

    public function select()
    {
        return $this->jsonReturn($this->body);
    }
}
