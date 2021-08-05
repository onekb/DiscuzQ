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

namespace App\Api\Controller\UsersV3;

use App\Censor\Censor;
use App\Common\ResponseCode;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;
use Discuz\Base\DzqLog;
use Discuz\Foundation\EventsDispatchTrait;
use Discuz\Auth\AssertPermissionTrait;

class CheckController extends DzqController
{
    use EventsDispatchTrait;
    use AssertPermissionTrait;

    protected $censor;

    public function __construct(Censor $censor)
    {
        $this->censor = $censor;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }


    public function main()
    {
        try {
            $username = $this->inPut('username');
            $nickname = $this->inPut('nickname');
            if (!empty($username)) {
                $this->checkIsRepeat('username', $username);
            }
            if (!empty($nickname)) {
                $this->checkIsRepeat('nickname', $nickname);
            }
            $this->outPut(ResponseCode::SUCCESS);
        } catch (\Exception $e) {
            DzqLog::error('username_nickname_check_api_error', [
                'username' => $this->inPut('username'),
                'nickname' => $this->inPut('nickname')
            ], $e->getMessage());
            $this->outPut(ResponseCode::INTERNAL_ERROR, '用户昵称检测接口异常');
        }
    }

    public function checkIsRepeat($fieled = '', $fieledVal = ''){
        $msg = $fieled == 'username' ? '用户名' : '昵称';
        //去除字符串中空格
        $fieledVal = str_replace(' ', '', $fieledVal);
        //敏感词检查
        $this->censor->checkText($fieledVal, $fieled);
        if(strlen($fieledVal) == 0) {
            $this->outPut(ResponseCode::USERNAME_NOT_NULL, $msg.'不能为空');
        }
        //长度检查
        if(mb_strlen($fieledVal,'UTF8') > 15){
            $this->outPut(ResponseCode::NAME_LENGTH_ERROR, $msg.'长度超过15个字符');
        }
        //重名检查
        $userNameCount = User::query()->where($fieled, $fieledVal)->count('id');
        if($userNameCount > 0){
            $this->outPut(ResponseCode::USERNAME_HAD_EXIST, $msg.'已经存在');
        }
    }

}
