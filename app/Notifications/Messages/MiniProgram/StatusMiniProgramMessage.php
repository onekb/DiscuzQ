<?php

namespace App\Notifications\Messages\MiniProgram;

use App\Models\User;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Arr;

/**
 * 根据用户状态变更 发送不同的通知 - 小程序
 * Class StatusMiniProgramMessage
 *
 * @package App\Notifications\Messages\MiniProgram
 */
class StatusMiniProgramMessage extends SimpleMessage
{
    /**
     * @var User $user
     */
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
        return ['content' => $this->getMiniProgramContent($this->data)];
    }

    protected function titleReplaceVars()
    {
        return [];
    }

    public function contentReplaceVars($data)
    {
        $reason = '无';
        if (Arr::has($data, 'refuse')) {
            if (! empty($data['refuse'])) {
                $reason = $data['refuse'];
            }
        }

        /**
         * 设置父类 模板数据
         * @parem $user_id 用户ID
         * @parem $user_name 用户名
         * @parem $user_mobile 用户手机号
         * @parem $user_mobile_encrypt 用户手机号(带 * 的)
         * @parem $user_change_status 改变的用户状态
         * @parem $user_original_status 原用户状态
         * @parem $reason 原因
         */
        $this->setTemplateData([
            '{$user_id}'              => $this->user->id,
            '{$user_name}'            => $this->user->username,
            '{$user_mobile}'          => $this->user->getRawOriginal('mobile'),
            '{$user_mobile_encrypt}'  => $this->user->mobile,
            '{$user_change_status}'   => User::enumStatus($this->user->status, true),
            '{$user_original_status}' => User::enumStatus($this->user->getRawOriginal('status'), true),
            '{$reason}'               => $reason,
        ]);

        // build data
        return $this->compiledArray();
    }

}
