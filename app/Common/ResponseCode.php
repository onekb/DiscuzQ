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

    const NOT_INSTALL = -10001;

    const WECHAT_INVALID_UNKNOWN_URL_EXCEPTION = -2001;
    const WECHAT_INVALID_CONFIG_EXCEPTION = -2002;
    const WECHAT_RUNTIME_EXCEPTION = -2003;
    const WECHAT_INVALID_ARGUMENT_EXCEPTION = -2004;


    const JUMP_TO_LOGIN = -3001;
    const JUMP_TO_REGISTER = -3002;
    const JUMP_TO_AUDIT = -3003;
    const JUMP_TO_HOME_INDEX = -3004;
    const SITE_CLOSED = -3005;
    const JUMP_TO_PAY_SITE = -3006;
    const JUMP_TO_SIGIN_FIELDS = -3007;

    const INVALID_PARAMETER = -4001;
    const UNAUTHORIZED = -4002;
    const RESOURCE_EXIST = -4003;
    const RESOURCE_NOT_FOUND = -4004;
    const RESOURCE_IN_USE = -4005;
    const CONTENT_BANNED = -4006;
    const VALIDATE_REJECT = -4007;
    const VALIDATE_IGNORE = -4008;
    const USER_BAN = -4009;
    const RESOURCE_EXPIRED = -4010;
    const INVALID_TOKEN = -4011;

    const NET_ERROR = -5001;
    const INTERNAL_ERROR = -5002;
    const DB_ERROR = -5003;
    const EXTERNAL_API_ERROR = -5004;
    const CENSOR_NOT_PASSED = -5005;

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
    const MINI_PROGRAM_GET_ACCESS_TOKEN_ERROR = -7009;
    const MINI_PROGRAM_QR_CODE_ERROR = -7010;
    const PC_BIND_ERROR = -7011;
    const MINI_PROGRAM_SCHEME_ERROR = -7012;
    const DECRYPT_CODE_FAILURE = -7013;

    const NEED_BIND_USER_OR_CREATE_USER = -7016;

    const REGISTER_DECRYPT_CODE_FAILED = -7014;
    const NOT_AUTHENTICATED = -7015; //未用到
    const PC_REBIND_ERROR = -7017;

    const MOBILE_IS_ALREADY_BIND = -7031;
    const REGISTER_CLOSE = -7032;
    const REGISTER_TYPE_ERROR = -7033;
    const USER_UPDATE_ERROR = -7034;
    const VERIFY_OLD_PHONE_NUMBER = -7035;
    const ENTER_NEW_PHONE_NUMBER = -7036;
    const ACCOUNT_HAS_BEEN_BOUND = -7037;
    const ACCOUNT_WECHAT_IS_NULL = -7038;
    const BIND_ERROR = -7039;
    const LOGIN_FAILED = -7040;
    const NAME_LENGTH_ERROR = -7041;
    const USERNAME_HAD_EXIST = -7042;
    const SMS_SERVICE_ENABLED = -7043;
    const BIND_TYPE_IS_NULL = -7044;
    const AUTH_INFO_HAD_EXPIRED = -7045;
    const USER_BIND_TYPE_IS_NULL = -7046;
    const PARAM_IS_NOT_OBJECT = -7047;
    const TRANSITION_NOT_OPEN = -7048;
    const USERNAME_NOT_NULL = -7049;
    const USER_LOGIN_STATUS_NOT_NULL = -7050;
    const NONSUPPORT_WECHAT_REBIND = -7051;
    const USERID_NOT_ALLOW_NULL = -7052;
    const USER_MOBILE_NOT_ALLOW_NULL = -7053;
    const REALNAME_NOT_NULL = -7054;
    const IDENTITY_NOT_NULL = -7055;
    const REAL_USER_CHECK_FAIL = -7056;
    const NICKNAME_CENSOR_NOT_PASSED = -7057;
    const USER_SINGATURE_LINIT_ERROR = -7058;
    const NOT_FOLLOW_YOURSELE = -7059;
    const NOT_FOLLOW_USER = -7060;
    const HAS_BEEN_BLOCKED_BY_THE_OPPOSITION = -7061;
    const USERNAME_OR_PASSWORD_ERROR = -7062;
    const NOT_REBIND_SELF_MOBILE = -7063;
    const NONSUPPORT_MOBILE_BIND = -7064;
    const NONSUPPORT_MOBILE_REBIND = -7065;
    const ORIGINAL_USER_MOBILE_VERIFY_ERROR = -7066;
    const PASSWORD_ILLEGALITY = -7067;
    const YOU_BLOCKED_HIM = -7068;
    const PASSWORD_NOT_ALLOW_HAS_SPACE = -7069;
    const USER_NEED_SIGNIN_FIELDS = -7070;
    const USER_IN_REVIEW = -7071;
    const PAY_JOIN_SITE = -7072;
    const USERNAME_NOT_ALLOW_HAS_SPACE = -7073;
    const TRY_LOGIN_AGAIN = -7074;
    const NOT_ALLOW_CENSOR_IMAGE = -7075;
    const CATEGORY_NOT_FOUNF = -7076;
    const CURRENT_IS_PAY_SITE = -7077;


    const NEED_BIND_WECHAT = -8000;
    const NEED_BIND_PHONE = -8001;
    const SMS_NOT_OPEN = -9001;
    const SMS_CODE_ERROR = -9002;
    const SMS_CODE_EXPIRE = -9003;

    const PAY_ORDER_FAIL = -10000;

    public static $codeMap = [
        self::NOT_INSTALL => '当前站点未安装',
        self::SITE_CLOSED => '站点已关闭',
        self::JUMP_TO_LOGIN => '跳转到登录页',
        self::JUMP_TO_AUDIT => '跳转到审核页',
        self::JUMP_TO_HOME_INDEX => '跳转到首页',
        self::JUMP_TO_REGISTER => '跳转到注册页',
        self::JUMP_TO_PAY_SITE => '跳转到站点付费页',
        self::JUMP_TO_SIGIN_FIELDS => '跳转到扩展字段页',
        self::SUCCESS => '接口调用成功',
        self::INVALID_PARAMETER => '参数错误',
        self::UNAUTHORIZED => '没有权限',
        self::RESOURCE_EXIST => '资源已存在',
        self::RESOURCE_NOT_FOUND => '资源不存在',
        self::RESOURCE_IN_USE => '资源被占用',
        self::CONTENT_BANNED => '内容被禁用',
        self::VALIDATE_REJECT => '审核不通过',
        self::VALIDATE_IGNORE => '忽略审核',
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
        self::MINI_PROGRAM_GET_ACCESS_TOKEN_ERROR => '全局token获取失败',
        self::MINI_PROGRAM_QR_CODE_ERROR => '小程序二维码生成失败',
        self::PC_BIND_ERROR => '绑定失败',
        self::MINI_PROGRAM_SCHEME_ERROR => '生成scheme失败',
        self::DECRYPT_CODE_FAILURE => '解密邀请码失败',
        self::MOBILE_IS_ALREADY_BIND => '手机号已被绑定',
        self::REGISTER_CLOSE => '站点关闭注册',
        self::REGISTER_TYPE_ERROR => '注册类型错误',
        self::USER_UPDATE_ERROR => '不可以使用相同的密码',
        self::VERIFY_OLD_PHONE_NUMBER => '请验证旧的手机号',
        self::ENTER_NEW_PHONE_NUMBER => '请输入新的手机号',
        self::ACCOUNT_HAS_BEEN_BOUND => '账户已经被绑定',
        self::ACCOUNT_WECHAT_IS_NULL => '账户微信为空',
        self::BIND_ERROR => '绑定错误',
        self::NEED_BIND_USER_OR_CREATE_USER => '需要绑定或注册用户',
        self::CENSOR_NOT_PASSED => '敏感词校验未通过',
        self::REGISTER_DECRYPT_CODE_FAILED => '解密邀请码失败',
        self::NAME_LENGTH_ERROR => '用户名或昵称长度超过15个字符',
        self::USERNAME_HAD_EXIST => '用户名已经存在',
        self::SMS_SERVICE_ENABLED => '短信服务未开启',
        self::NEED_BIND_WECHAT => '需要绑定微信',
        self::NEED_BIND_PHONE => '需要绑定手机',
        self::BIND_TYPE_IS_NULL => '绑定类型不存在',
        self::AUTH_INFO_HAD_EXPIRED => '授权信息已过期，请重新授权',
        self::USER_BIND_TYPE_IS_NULL => '用户绑定类型不存在',
        self::USER_BAN => '用户已被封禁',
        self::PARAM_IS_NOT_OBJECT => '参数不为对象',
        self::TRANSITION_NOT_OPEN => '过渡开关未开启',
        self::SMS_NOT_OPEN => '短信未开启',
        self::SMS_CODE_ERROR => '验证码错误',
        self::SMS_CODE_EXPIRE => '验证码已过期',
        self::USERNAME_NOT_NULL => '用户名不能为空',
        self::USER_LOGIN_STATUS_NOT_NULL => '用户登录态不能为空', // 已去除
        self::NONSUPPORT_WECHAT_REBIND => '该网站暂不支持微信换绑功能',
        self::NONSUPPORT_MOBILE_BIND => '该网站暂不支持手机绑定功能',
        self::NONSUPPORT_MOBILE_REBIND => '该网站暂不支持手机换绑功能',
        self::PC_REBIND_ERROR => '换绑失败',
        self::USERID_NOT_ALLOW_NULL => '用户id不允许为空',
        self::USER_MOBILE_NOT_ALLOW_NULL => '用户手机号不允许为空',
        self::PAY_ORDER_FAIL => '支付失败',
        self::WECHAT_INVALID_UNKNOWN_URL_EXCEPTION => '无效未知url地址',
        self::WECHAT_INVALID_CONFIG_EXCEPTION => '无效配置',
        self::WECHAT_INVALID_ARGUMENT_EXCEPTION => '无效参数',
        self::WECHAT_RUNTIME_EXCEPTION => '运行时异常',
        self::REALNAME_NOT_NULL => '真实姓名不能为空',
        self::IDENTITY_NOT_NULL => '身份证不能为空',
        self::REAL_USER_CHECK_FAIL => '实名认证不通过',
        self::USER_SINGATURE_LINIT_ERROR => '用户签名限制错误',
        self::NICKNAME_CENSOR_NOT_PASSED => '昵称未通过敏感词校验',
        self::NOT_FOLLOW_YOURSELE => '不能关注自己',
        self::NOT_FOLLOW_USER => '关注用户不存在',
        self::HAS_BEEN_BLOCKED_BY_THE_OPPOSITION => '根据对方的设置，您无法进行该操作',
        self::USERNAME_OR_PASSWORD_ERROR => '用户名或密码错误',
        self::LOGIN_FAILED => '登录失败',
        self::NOT_REBIND_SELF_MOBILE => '不能换绑自己的手机号',
        self::ORIGINAL_USER_MOBILE_VERIFY_ERROR => '原有手机号验证码处理失败',
        self::PASSWORD_ILLEGALITY => '密码输入非法',
        self::YOU_BLOCKED_HIM => '根据你的设置，您无法进行该操作',
        self::USERNAME_NOT_ALLOW_HAS_SPACE => '用户名不允许包含空格',
        self::PASSWORD_NOT_ALLOW_HAS_SPACE => '密码不允许包含空格',
        self::USER_NEED_SIGNIN_FIELDS => '用户需填写扩展字段', // 已去除
        self::USER_IN_REVIEW => '用户审核中', // 已去除
        self::PAY_JOIN_SITE => '请付费加入站点',
        self::TRY_LOGIN_AGAIN => '当前注册人数过多，请稍后登录',
        self::RESOURCE_EXPIRED => '资源已过期',
        self::NOT_ALLOW_CENSOR_IMAGE => '不允许上传敏感图',
        self::CATEGORY_NOT_FOUNF =>"分类不存在",
        self::INVALID_TOKEN => '无效token',
        self::CURRENT_IS_PAY_SITE =>'当前站点是付费模式'

    ];
}
