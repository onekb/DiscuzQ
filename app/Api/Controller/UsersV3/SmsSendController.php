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

use App\Common\ResponseCode;
use App\Models\MobileCode;
use App\Repositories\MobileCodeRepository;
use App\Repositories\UserRepository;
use App\Rules\Captcha;
use App\SmsMessages\SendCodeMessage;
use Discuz\Base\DzqLog;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Qcloud\QcloudTrait;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Carbon;

class SmsSendController extends AuthBaseController
{
    use QcloudTrait;

    const CODE_EXCEPTION = 5; //单位：分钟
    const CODE_INTERVAL = 60; //单位：秒

    protected $validation;
    protected $mobileCodeRepository;
    protected $settings;
    protected $type = [
        'login',
        'bind',
        'rebind',
        'reset_pwd',
        'reset_pay_pwd',
        'verify',
        'update'
    ];

    public function __construct(
        ValidationFactory       $validation,
        MobileCodeRepository    $mobileCodeRepository,
        SettingsRepository      $settings
    ) {
        $this->validation           = $validation;
        $this->mobileCodeRepository = $mobileCodeRepository;
        $this->settings             = $settings;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return true;
    }

    public function main()
    {
        try {
            $actor              = $this->user;
            $mobile             = $this->inPut('mobile');
            $type               = $this->inPut('type');
            $captchaTicket      = $this->inPut('captchaTicket');
            $captchaRandStr     = $this->inPut('captchaRandStr');
            $ip                 = ip($this->request->getServerParams());

            $paramData = [
                'mobile'         => $this->inPut('mobile'),
                'type'           => $this->inPut('type'),
                'captchaTicket'  => $this->inPut('captchaTicket'),
                'captchaRandStr' => $this->inPut('captchaRandStr')
            ];

            $data = array();
            $data['mobile']     = $mobile;
            $data['type']       = $type;
            $data['captcha']    = [
                $captchaTicket,
                $captchaRandStr,
                $ip
            ];

            $this->dzqValidate($data, [
                'captcha'   => [new Captcha],//暂时注释方便联调走主流程
                'type'      => 'required|in:' . implode(',', $this->type)
            ]);

            // 直接使用用户手机号
            if ($type === 'verify' || $type === 'reset_pay_pwd') {
                $data['mobile'] = $actor->getRawOriginal('mobile');
                if (empty($data['mobile'])) {
                    $this->outPut(ResponseCode::USER_MOBILE_NOT_ALLOW_NULL);
                }
            }

            // 手机号验证规则
            if (!(bool)$this->settings->get('qcloud_sms', 'qcloud')) {
                // 未开启短信服务不发送短信
                $mobileRule = [
                    function ($attribute, $value, $fail) {
                        $this->outPut(ResponseCode::SMS_SERVICE_ENABLED);
                    },
                ];
            } elseif ($type == 'bind') {
                // 用户手机号为空才可发送绑定验证码
                if (!empty($actor->mobile)) {
                    $this->outPut(ResponseCode::MOBILE_IS_ALREADY_BIND);
                }

                $mobileRule = 'required';
            } elseif ($type == 'rebind') {
                // 如果是重新绑定，需要在验证旧手机后 10 分钟内
                $unverified = MobileCode::where('mobile', $actor->getRawOriginal('mobile'))
                    ->where('type', 'verify')
                    ->where('state', 1)
                    ->where('updated_at', '<', Carbon::now()->addMinutes(10))
                    ->doesntExist();
                $mobileRule = [
                    function ($attribute, $value, $fail) use ($actor, $unverified) {
                        if ($unverified) {
                            $this->outPut(ResponseCode::VERIFY_OLD_PHONE_NUMBER);
                        } elseif ($value == $actor->getRawOriginal('mobile')) {
                            $this->outPut(ResponseCode::ENTER_NEW_PHONE_NUMBER);
                        }
                    },
                    'required'
                ];
            } elseif (in_array($type, ['reset_pwd', 'reset_pay_pwd'])) {
                // 如果已经绑定，不能再发送绑定短息
                // 如果重设密码，必须要已绑定的手机号
                $mobileRule = 'required|exists:users,mobile';
            } else {
                $mobileRule = 'required';
            }

            $this->dzqValidate($data, [
                'mobile'    => $mobileRule
            ]);

            $mobileCode = $this->mobileCodeRepository->getSmsCode($data['mobile'], $type);

            // 验证码限频
            if (! empty($mobileCode->updated_at) && strtotime($mobileCode->updated_at) > (time() - 60)) {
                $second =  strtotime($mobileCode->updated_at) - time() + 60;
                $this->outPut(ResponseCode::RESOURCE_EXIST, '请在'.$second.'秒后重新发送验证码');
            }

            if (!is_null($mobileCode) && $mobileCode->exists) {
                $mobileCode = $mobileCode->refrecode(self::CODE_EXCEPTION, $ip);
            } else {
                $mobileCode = MobileCode::make($data['mobile'], self::CODE_EXCEPTION, $type, $ip);
            }

            $result = $this->smsSend($data['mobile'], new SendCodeMessage([
                'code'      => $mobileCode->code,
                'expire'    => self::CODE_EXCEPTION]
            ));

            if (isset($result['qcloud']['status']) && $result['qcloud']['status'] === 'success') {
                $mobileCode->save();
            }

            $this->outPut(ResponseCode::SUCCESS, '', ['interval' => self::CODE_INTERVAL]);
        } catch (\Exception $e) {
            if (isset($e->getExceptions()['qcloud'])) {
                $errCode = !empty($e->getExceptions()['qcloud']->getCode()) ? $e->getExceptions()['qcloud']->getCode() : 0000;
                $errMsg = !empty($e->getExceptions()['qcloud']->getMessage()) ? $e->getExceptions()['qcloud']->getMessage() : '';
                DzqLog::error('all_the_gateways_have_failed', [
                    'mobile' => $this->inPut('mobile'),
                    'errCode' => $errCode,
                    'errMsg' => $errMsg,
                    'getExceptions' => $e->getExceptions()
                ], $e->getMessage());
                $this->outPut(ResponseCode::NET_ERROR, '手机号日频率限制');
            }
            DzqLog::error('手机号发送接口异常', $paramData, $e->getMessage());
            $this->outPut(ResponseCode::INTERNAL_ERROR, '手机号发送接口异常');
        }
    }
}
