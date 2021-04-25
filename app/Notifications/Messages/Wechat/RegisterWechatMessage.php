<?php

namespace App\Notifications\Messages\Wechat;

use App\Models\User;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * 用户注册通知 - 微信
 */
class RegisterWechatMessage extends SimpleMessage
{
    /**
     * @var User $actor
     */
    protected $actor;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var SettingsRepository
     */
    protected $settings;

    public function __construct(SettingsRepository $settings, UrlGenerator $url)
    {
        $this->settings = $settings;
        $this->url = $url;
    }

    public function setData(...$parameters)
    {
        [$firstData, $actor] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;
        $this->actor = $actor;

        $this->template();
    }

    public function template()
    {
        return ['content' => $this->getWechatContent()];
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {
        if ($this->settings->get('site_mode') == 'pay') {
            $siteMode = '付费';
        } else {
            $siteMode = '免费';
        }

        /**
         * 设置父类 模板数据
         * @parem $user_id 注册人id (可用于站点第几名注册)
         * @parem $user_name 注册人用户名
         * @parem $user_mobile 注册人手机号
         * @parem $user_mobile_encrypt 注册人手机号(带 * 的)
         * @parem $user_group 注册人用户组
         * @parem $joined_at 付费加入时间
         * @parem $expired_at 付费到期时间
         * @parem $site_name 站点名称
         * @parem $site_title 站点标题
         * @parem $site_introduction 站点介绍
         * @parem $site_mode 站点模式 (付费/免费，用于提示用户"付费加入该站点")
         */
        $this->setTemplateData([
            '{$user_id}'             => $this->actor->id,
            '{$user_name}'           => $this->actor->username,
            '{$user_mobile}'         => $this->actor->getRawOriginal('mobile'),
            '{$user_mobile_encrypt}' => $this->actor->mobile,
            '{$user_group}'          => $this->actor->groups->pluck('name')->join('、'),
            '{$joined_at}'           => $this->actor->joined_at,
            '{$expired_at}'          => $this->actor->expired_at,
            '{$site_name}'           => $this->settings->get('site_name'),
            '{$site_title}'          => $this->settings->get('site_title'),
            '{$site_introduction}'   => $this->settings->get('site_introduction'),
            '{$site_mode}'           => $siteMode,
        ]);

        // build data
        $expand = [
            'redirect_url' => $this->url->to(''),
        ];

        return $this->compiledArray($expand);
    }

}
