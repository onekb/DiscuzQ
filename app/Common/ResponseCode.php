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

namespace App\Common;


class ResponseCode
{
    const SUCCESS = 0;

    const JUMP_TO_LOGIN = -3001;
    const JUMP_TO_REGISTER = -3002;
    const JUMP_TO_AUDIT = -3003;
    const JUMP_TO_HOME_INDEX = -3004;

    const INVALID_PARAMETER = -4001;
    const UNAUTHORIZED = -4002;
    const RESOURCE_EXIST = -4003;
    const RESOURCE_NOT_FOUND = -4004;
    const RESOURCE_IN_USE = -4005;

    const NET_ERROR = -5001;
    const INTERNAL_ERROR = -5002;
    const DB_ERROR = -5003;
    const EXTERNAL_API_ERROR = -5004;

    const UNKNOWN_ERROR = -6001;
    const DEBUG_ERROR = -6002;

    const PC_QRCODE_TIME_OUT = -7001;
    const PC_QRCODE_SCANNING_CODE = -7002;
    const PC_QRCODE_ERROR = -7003;
    const SESSION_TOKEN_EXPIRED = -7004;
    const NOT_FOUND_USER = -7005;
    const NOT_FOUND_USER_WECHAT = -7006;
    const PC_QRCODE_TIME_FAIL = -7007;
    const GEN_QRCODE_TYPE_ERROR = -7008;


    public static $codeMap = [
        self::JUMP_TO_LOGIN => '跳转到登录页',
        self::JUMP_TO_AUDIT=>'跳转到审核页',
        self::JUMP_TO_HOME_INDEX=>'跳转到首页',
        self::JUMP_TO_REGISTER =>'跳转到注册页',
        self::SUCCESS => '接口调用成功',
        self::INVALID_PARAMETER => '参数错误',
        self::UNAUTHORIZED => '没有权限',
        self::RESOURCE_EXIST => '资源已存在',
        self::RESOURCE_NOT_FOUND => '资源不存在',
        self::RESOURCE_IN_USE => '资源被占用',
        self::NET_ERROR => '网络错误',
        self::INTERNAL_ERROR => '内部系统错误',
        self::EXTERNAL_API_ERROR => '外部接口错误',
        self::DB_ERROR => '数据库错误',
        self::UNKNOWN_ERROR => '未知错误',
        self::DEBUG_ERROR => '调试错误',
        self::PC_QRCODE_TIME_OUT => '二维码已失效，扫码超时',
        self::PC_QRCODE_SCANNING_CODE => '扫码中',
        self::PC_QRCODE_ERROR => '扫码失败，请重新扫码',
        self::SESSION_TOKEN_EXPIRED => 'SESSION TOKEN过期',
        self::NOT_FOUND_USER => '未找到用户',
        self::NOT_FOUND_USER_WECHAT => '未找到微信用户',
        self::PC_QRCODE_TIME_FAIL => '扫码登录失败',
        self::GEN_QRCODE_TYPE_ERROR => '生成二维码参数类型错误',
    ];
}
