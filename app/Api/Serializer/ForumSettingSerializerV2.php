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

use App\Common\AuthUtils;
use App\Common\PermissionKey;
use App\Common\SettingCache;
use App\Models\User;
use App\Settings\ForumSettingField;
use App\Repositories\UserRepository;
use Discuz\Api\Serializer\AbstractSerializer;
use Discuz\Common\PubEnum;
use App\Settings\SettingsRepository;
use Discuz\Http\UrlGenerator;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;

class ForumSettingSerializerV2 extends AbstractSerializer
{
    protected $type = 'forums';

    protected $settings;

    protected $forumField;

    protected $userRepo;

    public function __construct(SettingsRepository $settings, ForumSettingField $forumField, SettingCache $settingcache, Request $request, UserRepository $userRepo)
    {
        $this->settings = $settings;
        $this->forumField = $forumField;
        $this->settingcache = $settingcache;
        $this->request = $request;
        $this->userRepo = $userRepo;
    }

    /**
     * @param object $user
     * @return array
     */
    public function getDefaultAttributes($user = null)
    {
        if($user){
            $actor = $user;
        }else{
            $actor = $this->getActor();
        }

        // 获取logo完整地址
        $favicon = $this->forumField->siteUrlSplicing($this->settings->get('favicon'));
        $logo = $this->forumField->siteUrlSplicing($this->settings->get('logo'));
        $headerLogo = $this->forumField->siteUrlSplicing($this->settings->get('header_logo'));
        $backgroundImage = $this->forumField->siteUrlSplicing($this->settings->get('background_image'));

        $port = $this->request->getUri()->getPort();
        $siteUrl = $this->request->getUri()->getScheme() . '://' . $this->request->getUri()->getHost().(in_array($port, [80, 443, null]) ? '' : ':'.$port);

        $site_skin = (int)$this->settingcache->getSiteSkin();
        if($site_skin == 1){
            $favicon = $favicon ?: app(UrlGenerator::class)->to('/favicon.ico');
        }else{
            $favicon = $favicon ?: app(UrlGenerator::class)->to('/favicon.png');
        }

        $editGroupPermission = $this->userRepo->canEditGroup($actor);

        // 控制用户名密码入口是否展示
        $registerType = $this->settings->get('register_type');
        if($registerType == 0) {
            $usernameLoginIsdisplay = true;
        } else {
            //存在未绑定任何第三方的信息用户，则展示用户名和密码登录
            $usernameLoginIsdisplay = false;
            if(User::query()->where('bind_type',AuthUtils::DEFAULT)->count('id') > 0){
                $usernameLoginIsdisplay = true;
            }
        }

        $attributes = [
            // 站点设置
            'set_site' => [
                'site_name' => $this->settings->get('site_name'),
                'site_title' => $this->settings->get('site_title'),
                'site_keywords' => $this->settings->get('site_keywords'),
                'site_introduction' => $this->settings->get('site_introduction'),
                'site_mode' => $this->settings->get('site_mode'), // pay public
                'open_ext_fields'=>$this->settings->get('open_ext_fields'),
                'site_close' => (bool)$this->settings->get('site_close'),
                'site_manage' => json_decode($this->settings->get('site_manage'), true),
                'api_freq'    => $actor->isAdmin()?json_decode($this->settings->get('api_freq'), true):null,
                'site_close_msg'=>$this->settings->get('site_close_msg'),
                'site_favicon' => $favicon,
                'site_logo' => $logo ?: '',
                'site_header_logo' => $headerLogo ?: '',
                'site_background_image' => $backgroundImage ?: '',
                'site_url' => $siteUrl,
                'site_stat' => $this->settings->get('site_stat') ?: '',
                'site_author' => User::query()->where('id', $this->settings->get('site_author'))->first(['id', 'username', 'avatar']),
                'site_install' => $this->settings->get('site_install'), // 安装时间
                'site_record' => $this->settings->get('site_record'),
                'site_cover' => $this->settings->get('site_cover') ?: '',
                'site_record_code' => $this->settings->get('site_record_code') ?: '',
                'site_master_scale' => $this->settings->get('site_master_scale'), // 站长比例
                'site_pay_group_close' => $this->settings->get('site_pay_group_close'), // 用户组购买开关
                'site_minimum_amount' => $this->settings->get('site_minimum_amount'),
                'site_open_sort' => $this->settings->get('site_open_sort') == "" ? 0 : (int)$this->settings->get('site_open_sort'),
                'site_can_reward'     => (bool) $this->settings->get('site_can_reward'),
                'usernameLoginIsdisplay' => $usernameLoginIsdisplay
            ],

            // 注册设置
            'set_reg' => [
                'register_close' => (bool)$this->settings->get('register_close'),
                'register_validate' => (bool)$this->settings->get('register_validate'),
                'register_captcha' => (bool)$this->settings->get('register_captcha'),
                'password_length' => (int)$this->settings->get('password_length'),
                'password_strength' => $this->settings->get('password_strength') === '' ? [] : explode(',', $this->settings->get('password_strength')),
                'register_type' => (int)$this->settings->get('register_type', 'default', 0),
                'is_need_transition' => (bool)$this->settings->get('is_need_transition'),
            ],

            // 第三方登录设置
            'passport' => [
                'offiaccount_open' => (bool)$this->settings->get('offiaccount_close', 'wx_offiaccount'), // 微信H5 开关
                'miniprogram_open' => (bool)$this->settings->get('miniprogram_close', 'wx_miniprogram'), // 微信小程序 开关
//                'oplatform_close' => (bool)$this->settings->get('oplatform_close', 'wx_oplatform'),       // 微信PC 开关
            ],

            // 支付设置
            'paycenter' => [
                'wxpay_close' => (bool)$this->settings->get('wxpay_close', 'wxpay'),
                'wxpay_ios' => (bool)$this->settings->get('wxpay_ios', 'wxpay'),
                'wxpay_mchpay_close' => (bool)$this->settings->get('wxpay_mchpay_close', 'wxpay'),
            ],

            // 附件设置
            'set_attach' => [
                'support_img_ext' => $this->settings->get('support_img_ext', 'default'),
                'support_file_ext' => $this->settings->get('support_file_ext', 'default'),
                'support_max_size' => $this->settings->get('support_max_size', 'default'),
            ],

            // 腾讯云设置
            'qcloud' => [
                'qcloud_app_id' => $this->settings->get('qcloud_app_id', 'qcloud'),
                'qcloud_close' => (bool)$this->settings->get('qcloud_close', 'qcloud'),
                'qcloud_cos' => (bool)$this->settings->get('qcloud_cos', 'qcloud'),
                'qcloud_captcha' => (bool)$this->settings->get('qcloud_captcha', 'qcloud'),
                'qcloud_captcha_app_id' => $this->settings->get('qcloud_captcha_app_id', 'qcloud'),
                'qcloud_faceid' => (bool)$this->settings->get('qcloud_faceid', 'qcloud'),
                'qcloud_sms' => (bool)$this->settings->get('qcloud_sms', 'qcloud'),
                'qcloud_vod' => (bool)$this->settings->get('qcloud_vod', 'qcloud'),
                'qcloud_cos_doc_preview' => (bool)$this->settings->get('qcloud_cos_doc_preview', 'qcloud'),
            ],

            // 提现设置
            'set_cash' => [
                'cash_rate' => $this->settings->get('cash_rate', 'cash'), // 提现费率
                'cash_min_sum' => $this->settings->get('cash_min_sum', 'cash') ?: '',
            ],

            // 其它信息(非setting中的信息)
            'other' => [
                // 基础信息
                'count_threads' => (int) $this->settings->get('thread_count'),          // 站点主题数
                'count_posts' => (int) $this->settings->get('post_count'),              // 站点回复数
                'count_users' => (int) $this->settings->get('user_count'),              // 站点用户数

                // 管理权限
                'can_edit_user_group'  => $editGroupPermission,                // 修改用户用户组
                'can_edit_user_status' => $editGroupPermission,                // 修改用户状态

                // 至少在一个分类下有发布权限
                'can_create_thread_in_category' => $actor->hasPermission('switch.'.PermissionKey::CREATE_THREAD),

                // 至少在一个分类下有查看主题列表权限 或 有全局查看权限
                'can_view_threads' => $actor->hasPermission('switch.'.PermissionKey::VIEW_THREADS),

                // 至少在一个分类下有免费查看付费帖子权限 或 有全局免费查看权限
                'can_free_view_paid_threads' => $actor->hasPermission('switch.'.PermissionKey::THREAD_FREE_VIEW_POSTS),

                // 发布权限
                'can_create_dialog'            => $this->userRepo->canCreateDialog($actor),               // 发短消息
                'can_invite_user_scale'        => $this->userRepo->canCreateInviteUserScale($actor),      // 发分成邀请
                'can_insert_thread_attachment' => $this->userRepo->canInsertAttachmentToThread($actor),   // 插入附件
                'can_insert_thread_paid'  => $this->userRepo->canInsertPayToThread($actor),               // 插入付费内容
                'can_insert_thread_video' => $this->userRepo->canInsertVideoToThread($actor),             // 插入视频
                'can_insert_thread_image' => $this->userRepo->canInsertImageToThread($actor),             // 插入图片
                'can_insert_thread_audio' => $this->userRepo->canInsertAudioToThread($actor),             // 插入语音
                'can_insert_thread_goods'      => $this->userRepo->canInsertGoodsToThread($actor),        // 插入商品
                'can_insert_thread_position'   => $this->userRepo->canInsertPositionToThread($actor),     // 插入位置
                'can_insert_thread_red_packet' => $this->userRepo->canInsertRedPacketToThread($actor),    // 插入红包
                'can_insert_thread_reward'     => $this->userRepo->canInsertRewardToThread($actor),       // 插入悬赏
                'can_insert_thread_anonymous'  => $this->userRepo->canCreateThreadAnonymous($actor),      // 允许匿名发布

                // 其他
                'initialized_pay_password'   => (bool) $actor->pay_password,                              // 是否初始化支付密码
                'create_thread_with_captcha' => $this->userRepo->canCreateThreadWithCaptcha($actor),      // 发布内容需要验证码
                'publish_need_bind_phone'    => $this->userRepo->canCreateThreadNeedBindPhone($actor),    // 发布内容需要绑定手机
            ],

            'lbs' => [
                'lbs' => (bool) $this->settings->get('lbs', 'lbs'),         // 位置服务开关
                'qq_lbs_key' => $this->settings->get('qq_lbs_key', 'lbs'),  // 腾讯位置服务 key
            ],

            'ucenter' => [
                'ucenter' => (bool) $this->settings->get('ucenter', 'ucenter'),
            ]
        ];

        // 站点开关 - 满足条件返回
        if ($attributes['set_site']['site_close'] == 1) {
            $attributes['set_site'] += $this->forumField->getSiteClose();
        }

        // 付费模式 - 满足条件返回
        if ($attributes['set_site']['site_mode'] == 'pay') {
            $attributes['set_site'] += $this->forumField->getSitePayment();
        }

        // 开启视频服务 - 满足条件返回
        if ($attributes['qcloud']['qcloud_close'] && $attributes['qcloud']['qcloud_vod']) {
            $attributes['qcloud'] += $this->forumField->getQCloudVod();
        } else {
            //未开启vod服务 不可发布视频主题
            $attributes['other']['can_insert_thread_video'] = false;
            $attributes['other']['can_insert_thread_audio'] = false;
        }

        // 微信小程序请求时判断视频开关
        $headers = $this->request->getHeaders();
        $headersStr = strtolower(json_encode($headers, 256));
        if (! $this->settings->get('miniprogram_video', 'wx_miniprogram') &&
            (strpos(Arr::get($this->request->getServerParams(), 'HTTP_X_APP_PLATFORM'), 'wx_miniprogram') !== false || strpos($headersStr, 'miniprogram') !== false ||
                strpos($headersStr, 'compress') !== false)) {
            $attributes['other']['can_insert_thread_video'] = false;
        }
        //判断三种注册方式是否置灰禁用, 3.0 无注册模式选择
//        $attributes['sign_enable']=$this->getSignInEnable($attributes);

        // 判断用户是否存在
        if ($actor->exists) {

            // 当前用户信息
            $attributes['user'] = [
                'groups' => $actor->groups,
                'register_time' => $this->formatDate($actor->created_at),
                'user_id' => $actor->id
            ];
            /*
            if(!empty($attributes['user']['groups'])){
                foreach ($attributes['user']['groups'] as &$group){
                    $group->pivot = $group->makeHidden(['user_id','group_id']);
                }
            }
            */


            // 当前用户是否是管理员 - 补充返回数据
            if ($actor->isAdmin()) {
                // 站点设置
                $attributes['set_site'] += $this->forumField->getSiteSettings();

                // 第三方登录设置
                $attributes['passport'] += $this->forumField->getPassportSettings();

                // 支付设置
                $attributes['paycenter'] += $this->forumField->getPaycenterSettings();

                // 腾讯云设置
                $attributes['qcloud'] += $this->forumField->getQCloudSettings();

                // 提现设置
                $attributes['set_cash'] += $this->forumField->getCashSettings();

                // 水印设置
                $attributes['watermark'] = $this->forumField->getWatermarkSettings();

                // UCenter设置
                $attributes['ucenter'] += $this->forumField->getUCenterSettings();

                // lbs 设置
                // $attributes['lbs'] += [ 'qq_lbs_key' => $this->settings->get('qq_lbs_key', 'lbs')];
            } else {
                $attributes['qcloud']['qcloud_vod_token'] = "";
            }
        } else {
            $attributes['qcloud']['qcloud_vod_token'] = "";
        }

        return $attributes;
    }

    /**
     *判断管理后台三个端的选项按钮是否禁用
     */
    private function getSignInEnable($attributes)
    {
        $siteManage = array_column($attributes['set_site']['site_manage'], null, 'key');
        $user_name = true;
        $mobile_phone = false;
        $wechat_direct = false;
        //配置了短信服务则允许使用手机号注册
        $attributes['qcloud']['qcloud_sms'] && $mobile_phone = true;
        //允许使用微信无感注册登陆
        //pc :1,h5:2,微信：3
        $p1 = [];
        $p2 = [];
        if ($siteManage[PubEnum::PC]['value']) {
            $p1[]=PubEnum::PC;
            if ($attributes['passport']['offiaccount_close'] && $attributes['passport']['oplatform_close']) {
                $p2[]=PubEnum::PC;
            }
        }
        if ($siteManage[PubEnum::H5]['value']) {
            $p1[]=PubEnum::H5;
            if ($attributes['passport']['offiaccount_close']) {
                $p2[]=PubEnum::H5;
            }
        }
        if ($siteManage[PubEnum::MinProgram]['value']) {
            $p1[]=PubEnum::MinProgram;
            if ($attributes['passport']['miniprogram_close']) {
                $p2[]=PubEnum::MinProgram;
            }
        }
        $p1 == $p2 && $wechat_direct = true;
        return [
            'user_name' => $user_name,
            'mobile_phone' => $mobile_phone,
            'wechat_direct' => $wechat_direct
        ];
    }


}
