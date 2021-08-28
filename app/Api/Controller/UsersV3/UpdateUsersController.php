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
use App\Commands\Users\UpdateClientUser;
use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Models\User;
use App\Repositories\UserRepository;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqController;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;

class UpdateUsersController extends DzqController
{

    public function prefixClearCache($user)
    {
        DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_USERS, $user->id);
    }

    protected $bus;
    protected $settings;
    protected $passwordLength = 6;
    protected $passwordStrength = [];
    protected $censor;

    public function __construct(Dispatcher $bus, SettingsRepository $settings, Censor $censor)
    {
        $this->bus = $bus;
        $this->settings = $settings;
        $this->censor = $censor;
    }

    // 权限检查
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $id = $this->user->id;
        if (empty($id)) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '');
        }
        $nickname = $this->inPut('nickname');
        $username = $this->inPut('username');
        $password = $this->inPut('password');
        $newPassword = $this->inPut('newPassword');
        $passwordConfirmation = $this->inPut('passwordConfirmation');
        $payPassword = $this->inPut('payPassword');
        $payPasswordConfirmation = $this->inPut('payPasswordConfirmation');
        $payPasswordToken = $this->inPut('payPasswordToken');

        $registerReason = $this->inPut('registerReason');

        $requestData = [];
        if (!empty($username)) {
            $requestData['username'] = $username;
            $checkUsername['username'] = $username;
            $this->dzqValidate($checkUsername, [
                'username' => [
                    'required',
                    'max:15',
                    'unique:users',
                    function ($attribute, $value, $fail) {
                        if ($value === '匿名用户') {
                            $fail('无效的用户名。');
                        }
                    },
                ]
            ]);
        }
        if (!empty($password)) {
            $requestData['password'] = $password;
            if (! $this->user->checkPassword($password)) {
                $this->outPut(ResponseCode::INVALID_PARAMETER,'原密码不匹配');
            }
        }
        if (!empty($newPassword)) {
            $requestData['newPassword'] = $newPassword;
            // 验证新密码与原密码不能相同
            if ($this->user->checkPassword($newPassword)) {
                $this->outPut(ResponseCode::INVALID_PARAMETER,'新密码与原密码不能相同');
            }
        }
        if (!empty($passwordConfirmation)) {
            $requestData['password_confirmation'] = $passwordConfirmation;
            $checkPassword['password'] = $newPassword;
            $checkPassword['password_confirmation'] = $passwordConfirmation;
            $this->dzqValidate($checkPassword, [
                'password' => $this->getPasswordRules(),
            ]);

            //密码强度格式判断
            if ($this->getPasswordStrength()) {
                collect($this->getPasswordStrength())->each(function ($regex) use (&$passwordRules) {
                    $passwordRules[] = self::optionalPasswordStrengthRegex[$regex]['pattern'];
                });
                foreach (self::optionalPasswordStrengthRegex as $k=>$val){
                    if(in_array($val['pattern'],$passwordRules)){
                        if(!preg_match($val['pattern'], $checkPassword['password'])){
                            $this->outPut(ResponseCode::INVALID_PARAMETER,'密码格式不正确,必须包含'.$val['name']);
                        }
                    }
                }
            }
        }
        if (!empty($payPassword)) {
            $requestData['payPassword'] = $payPassword;
        }
        if (!empty($payPasswordConfirmation)) {
            $requestData['pay_password_confirmation'] = $payPasswordConfirmation;
            $checkPayPassword['pay_password'] = $payPassword;
            $checkPayPassword['pay_password_confirmation'] = $payPasswordConfirmation;
            $this->dzqValidate($checkPayPassword, [
                'pay_password' => 'bail|sometimes|required|confirmed|digits:6',
            ]);
            if ($this->user->pay_password) {
                if ($this->user->checkWalletPayPassword($checkPayPassword['pay_password'])) {
                    $this->outPut(ResponseCode::INVALID_PARAMETER,'新密码与原密码不能相同');
                }
            }
        }
        if (!empty($payPasswordToken)) {
            $requestData['pay_password_token'] = $payPasswordToken;
            $checkPayPasswordToken['pay_password_token'] = $payPasswordToken;
            $this->dzqValidate($checkPayPasswordToken, [
                'pay_password_token' => 'sometimes|required|session_token:reset_pay_password,'.$this->user->id,
            ]);
        }

        $getRequestData = json_decode(file_get_contents("php://input"), TRUE);
        if (Arr::has($getRequestData, 'signature')){
            $requestData['signature'] = $this->inPut('signature');
        }

        if (!empty($registerReason)) {
            $requestData['register_reason'] = $registerReason;
        }

        if (isset($this->request->getParsedBody()['nickname'])){
            if (empty($nickname)) {
                $this->outPut(ResponseCode::INVALID_PARAMETER,'昵称不能为空');
            }
            $isHasSpace = strpos($nickname,' ');
            if ($isHasSpace !== false) {
                $this->outPut(ResponseCode::USERNAME_NOT_ALLOW_HAS_SPACE, '昵称不允许包含空格');
            }
            if (mb_strlen($nickname, 'UTF8') > 15) {
                $this->outPut(ResponseCode::NAME_LENGTH_ERROR, '昵称长度超过15个字符');
            }
            $isExists = User::query()->where('nickname', $nickname)->where('id', '<>', $id)->exists();
            if (!empty($isExists)) {
                $this->outPut(ResponseCode::USERNAME_HAD_EXIST, '昵称已经存在');
            }
            $this->censor->checkText($nickname,'nickname');
            if (!empty($nickname)) {
                $requestData['nickname'] = $nickname;
            }

        }

        $result = $this->bus->dispatch(
            new UpdateClientUser(
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
        $returnData['threadCount'] = $data['threadCount'];
        $returnData['followCount'] = $data['followCount'];
        $returnData['fansCount'] = $data['fansCount'];
        $returnData['likedCount'] = $data['likedCount'];
        $returnData['questionCount'] = $data['questionCount'];
        $returnData['avatar'] = $data['avatar'];
        $returnData['background'] = "";
        if (!empty($data['background'])) {
            $returnData['background'] = $this->getBackground($data['background']);
        }
        $returnData['signature'] = $data['signature'];

        $this->outPut(ResponseCode::SUCCESS, '', $returnData);
    }

    protected function getPasswordRules()
    {
        $passwordLength = $this->getPasswordLength();
        $rules = [
            'required',
            'max:50',
            'min:' . $passwordLength,
            'confirmed'
        ];
        return $rules;
    }

    protected function getPasswordLength(){
        $settings = $this->settings;

        // 获取后台设置的密码长度
        $settingsPasswordLength = (int) $settings->get('password_length');

        // 获取后台设置的密码强度
        $settingsPasswordStrength = explode(',', trim($settings->get('password_strength'), ','));

        // 后台设置的长度大于默认长度时，使用后台设置的长度
        $this->passwordLength = $settingsPasswordLength > $this->passwordLength
            ? $settingsPasswordLength
            : $this->passwordLength;

        // 使用后台设置的密码强度
        return $this->passwordLength;
    }

    protected function getPasswordStrength(){
        $settings = $this->settings;
        // 获取后台设置的密码强度
        $settingsPasswordStrength = explode(',', trim($settings->get('password_strength'), ','));
        return $settingsPasswordStrength ?: $this->passwordStrength;
    }

    const optionalPasswordStrengthRegex = [
        [
            'name' => '数字',
            'pattern' => '/\d+/',
        ],
        [
            'name' => '小写字母',
            'pattern' => '/[a-z]+/',
        ],
        [
            'name' => '符号',
            'pattern' => '/[^a-zA-Z0-9]+/',
        ],
        [
            'name' => '大写字母',
            'pattern' => '/[A-Z]+/',
        ],
    ];

    protected function getBackground($background)
    {
        $url = $this->request->getUri();
        $port = $url->getPort();
        $port = $port == null ? '' : ':' . $port;
        $path = $url->getScheme() . '://' . $url->getHost() . $port . '/';
        $returnData['background'] = $path . "/storage/background/" . $background;
        if (strpos($background, "cos://") !== false) {
            $background = str_replace("cos://", "", $background);
            $remoteServer = $this->settings->get('qcloud_cos_cdn_url', 'qcloud', true);
            $right = substr($remoteServer, -1);
            if ("/" == $right) {
                $remoteServer = substr($remoteServer, 0, strlen($remoteServer) - 1);
            }
            $returnData['background'] = $remoteServer . "/public/background/" . $background;
        }
        return $returnData['background'];
    }
}
