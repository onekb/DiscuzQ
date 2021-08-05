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

namespace App\Api\Controller\ThreadsV3;


use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class TestController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        $a = $this->inPut('a');
        $b = $this->inPut('b');
        $t1 = DISCUZ_START;
        $t2 = microtime(true);
        $this->info('TestController',[
            $t1,
            $t2,
            $t2 - $t1,
            $this->user->toArray(),
            $a,$b
        ]);
        $this->outPut(ResponseCode::SUCCESS, '', [
            $t1,
            $t2,
            $t2 - $t1,
            '随着经济社会的持续发展，高速公路建设、管养、运营等领域，也不断注入新的发展理念，从“生态高速”到“智慧高速”，再到“平安高速”，沈海高速打造了一道又一道靓丽风景线，为沿线地区高质量发展赋能增效。（沙芳如）'
        ]);
    }
}
