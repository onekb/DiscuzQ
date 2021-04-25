<?php

namespace App\Notifications\Messages\Wechat;

use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * 用户角色调整通知 - 微信
 */
class GroupWechatMessage extends SimpleMessage
{
    protected $user;

    protected $data;

    /**
     * @var UrlGenerator
     */
    protected $url;

    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    public function setData(...$parameters)
    {
        [$firstData, $user, $data] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;
        $this->user = $user;
        $this->data = $data;

        $this->template();
    }

    public function template()
    {
        return ['content' => $this->getWechatContent($this->data)];
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {
        /**
         * 设置父类 模板数据
         * @parem $user_id 被更改人的用户ID
         * @parem $user_name 被更改人的用户名
         * @parem $user_mobile 被更改人的手机号
         * @parem $user_mobile_encrypt 被更改人的手机号(带 * 的)
         * @parem $group_original 原用户组名
         * @parem $group_change 新用户组名
         */
        $this->setTemplateData([
            '{$user_id}'             => $this->user->id,
            '{$user_name}'           => $this->user->username,
            '{$user_mobile}'         => $this->user->getRawOriginal('mobile'),
            '{$user_mobile_encrypt}' => $this->user->mobile,
            '{$group_original}'      => $data['old']->pluck('name')->join('、'),
            '{$group_change}'        => $data['new']->pluck('name')->join('、'),
        ]);

        // build data
        $expand = [
            'redirect_url' => $this->url->to(''),
        ];

        return $this->compiledArray($expand);
    }

}
