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

namespace App\Listeners\User;

use App\Models\User;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Http\DiscuzResponseFactory;

class ValidateLogin
{
    public function handle($event)
    {
        $user = $event->user;
        if ($user->status == 2) {
            $this->exceptionResponse($user->id, 'register_validate');
//            throw new PermissionDeniedException('register_validate');
        }
    }

    private function exceptionResponse($userId, $msg)
    {
        $crossHeaders = DiscuzResponseFactory::getCrossHeaders();
        foreach ($crossHeaders as $k => $v) {
            header($k . ':' . $v);
        }
        $response = [
            'errors' => [
                [
                    'status' => '401',
                    'code' => $msg,
                    'data' => User::getUserReject($userId)
                ]
            ]
        ];
        header('Content-Type:application/json; charset=utf-8', true, 401);
        exit(json_encode($response, 256));
    }
}
