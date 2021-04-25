<?php

namespace App\Notifications\Messages\Wechat;

use App\Models\User;
use App\Models\UserWalletCash;
use Discuz\Notifications\Messages\SimpleMessage;
use Illuminate\Contracts\Routing\UrlGenerator;

/**
 * 提现通知/提现失败通知 - 微信
 * Class WithdrawalWechatMessage
 *
 * @package App\Notifications\Messages\Wechat
 */
class WithdrawalWechatMessage extends SimpleMessage
{
    /**
     * @var UserWalletCash $cash
     */
    protected $cash;

    /**
     * @var User $actor
     */
    protected $actor;

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
        [$firstData, $actor, $cash] = $parameters;
        // set parent tpl data
        $this->firstData = $firstData;

        $this->actor = $actor;
        $this->cash = $cash;

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
        /**
         * 设置父类 模板数据
         * @parem $user_id 提现用户ID
         * @parem $user_name 提现用户
         * @parem $cash_sn 提现交易编号
         * @parem $cash_charge 提现手续费
         * @parem $cash_actual_amount 提现实际到账金额
         * @parem $cash_apply_amount 提现申请金额
         * @parem $cash_status 提现结果 (待审核/审核通过/审核不通过/待打款/已打款/打款失败)
         * @parem $cash_mobile 提现到账手机号码
         * @parem $remark 备注或原因 (默认"无")
         * @parem $trade_no 交易号
         * @parem $cash_created_at 提现创建时间
         */
        $this->setTemplateData([
            '{$user_id}'            => $this->cash->user->id,
            '{$user_name}'          => $this->cash->user->username,
            '{$cash_sn}'            => $this->cash->cash_sn,
            '{$cash_charge}'        => $this->cash->cash_charge,
            '{$cash_actual_amount}' => $this->cash->cash_actual_amount,
            '{$cash_apply_amount}'  => $this->cash->cash_apply_amount,
            '{$cash_status}'        => UserWalletCash::enumCashStatus($this->cash->cash_status),
            '{$cash_mobile}'        => $this->cash->cash_mobile,
            '{$remark}'             => $this->cash->remark ?: '无',
            '{$trade_no}'           => $this->cash->trade_no,
            '{$cash_created_at}'    => $this->cash->created_at,
        ]);

        // build data
        $expand = [
            'redirect_url' => $this->url->to(''),
        ];

        return $this->compiledArray($expand);
    }

}
