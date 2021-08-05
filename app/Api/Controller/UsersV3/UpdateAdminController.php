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

use App\Commands\Users\UpdateAdminUser;
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\Setting;
use App\Repositories\UserRepository;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Bus\Dispatcher;

class UpdateAdminController extends DzqController
{

    protected $bus;
    protected $settings;

    public function __construct(Dispatcher $bus,SettingsRepository $settings)
    {
        $this->bus = $bus;
        $this->settings = $settings;
    }

    // 权限检查
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $id = $this->inPut('id');
        if(empty($id)){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'用户id不能为空');
        }
        $username = $this->inPut('username');
        $nickname = $this->inPut('nickname');
        $password = $this->inPut('password');
        $newPassword = $this->inPut('newPassword');
        $mobile = $this->inPut('mobile');
        $status = $this->inPut('status');
        $expire_at = $this->inPut('expiredAt');
        $groupId = $this->inPut('groupId');

        $requestData = [];
        if(!empty($username)){
            $requestData['username'] = $username;
        }

        if (!empty($nickname)) {
            $requestData['nickname'] = $nickname;
        }

        if(!empty($password)){
            $requestData['password'] = $password;
        }
        if(!empty($newPassword)){
            $requestData['newPassword'] = $newPassword;
            $this->processPassword($newPassword);
        }

        $requestData['mobile'] = $mobile;
        $requestData['status'] = $status;

        if(!empty($expire_at)){
            $requestData['expired_at'] = $expire_at;
        }
        if(!empty($groupId)){
            $requestData['groupId'] = $groupId;
        }

        $this->dzqValidate($requestData, [
            'username'=> 'required|max:15'
        ]);

        $result = $this->bus->dispatch(
            new UpdateAdminUser(
                $id,
                $requestData,
                $this->user
            )
        );
        $data = $this->camelData($result);
        $returnData = [];
        $returnData['id'] = $data['id'];
        $returnData['username'] = $data['username'];
        $returnData['nickname'] = $data['nickname'];
        $returnData['mobile'] = $data['mobile'];
        $returnData['status'] = $data['status'];
        $returnData['avatar'] = $data['avatar'];
        $returnData['expiredAt'] = $data['expiredAt'];
        $returnData['registerIp'] = $data['registerIp'];
        $returnData['createdAt'] = $data['createdAt'];
        $returnData['lastLoginIp'] = $data['lastLoginIp'];
        $returnData['loginAt'] = $data['loginAt'];

        return $this->outPut(ResponseCode::SUCCESS,'', $returnData);
    }

    public function prefixClearCache($user)
    {
        $id = $this->inPut('id');
        if(empty($id)){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'用户id不能为空');
        }
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_GROUP_USER,$id);
    }

    public function processPassword($newPassword)
    {
        $passwordLength = (int)$this->settings->get('password_length');
        $psdLen = mb_strlen($newPassword);
        $psdMinLen = $passwordLength > 6 ? $passwordLength : 6;
        $pasMaxLen = 18;
        if ($psdMinLen > $psdLen || $pasMaxLen < $psdLen) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, "密码长度必须大于{$psdMinLen},小于{$pasMaxLen}位");
        }
        $passwordStrength = trim($this->settings->get('password_strength'));
        if (!empty($passwordStrength)){
            $passwordStrength = explode(',',$passwordStrength);

            foreach ($passwordStrength as $v) {
                if ($v == Setting::DIGIEAL) {
                    $digital = preg_match('/[0-9]/', $newPassword);
                    if (empty($digital)) {
                        $this->outPut(ResponseCode::INVALID_PARAMETER, '请在登录密码中填写数字');
                    }
                }

                if ($v == Setting::LOWER_CASE_LETTERS) {
                    $lowerCaseLetters = preg_match("/[a-z]/", $newPassword);
                    if (empty($lowerCaseLetters)) {
                        $this->outPut(ResponseCode::INVALID_PARAMETER, '请在登录密码中填写小写字母');
                    }
                }

                if ($v == Setting::SYMBOL) {
                    $uppercaseLetter = preg_match("/^[\u4e00-\u9fa5a-zA-z\d]+$/",$newPassword);
                    if (!empty($uppercaseLetter)) {
                        $this->outPut(ResponseCode::INVALID_PARAMETER, '请在登录密码中填写特殊符号');
                    }
                }

                if ($v == Setting::UPPERCASE_LETTER) {
                    $specialSymbol = preg_match("/[A-Z]/", $newPassword);
                    if (empty($specialSymbol)) {
                        $this->outPut(ResponseCode::INVALID_PARAMETER, '请在登录密码中填写大写字母');
                    }
                }
            }
        }
    }
}
