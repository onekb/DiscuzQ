<?php

namespace App\Api\Controller\OrderV3;

use App\Common\ResponseCode;
use App\Common\Utils;
use App\Repositories\UserRepository;
use Exception;
use App\Models\Group;
use App\Models\Order;
use App\Models\OrderChildren;
use App\Models\Thread;
use App\Models\PayNotify;
use App\Settings\SettingsRepository;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqController;

class CreateOrderController extends DzqController
{
    protected $settings;

    public function __construct(SettingsRepository $settings)
    {
        $this->settings = $settings;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$userRepo->canCreateOrder($this->user)) {
            throw new PermissionDeniedException('没有创建订单权限');
        }

        if ($this->inPut('type') == Order::ORDER_TYPE_REWARD){
            if(empty($this->settings->get('site_can_reward'))) throw new PermissionDeniedException('站点没有开启打赏');
        }
        return true;
    }

    public function main()
    {
        $data = [
            'amount' => (float) $this->inPut('amount'),
            'red_amount' => (float) $this->inPut('redAmount') ?? 0,
            'reward_amount' => (float) $this->inPut('rewardAmount') ?? 0,
            'is_anonymous' => (int) $this->inPut('isAnonymous'),
            'type' => (int) $this->inPut('type'),
            'thread_id' => (int) $this->inPut('threadId') ?? '',
            'group_id' => (int) $this->inPut('groupId') ?? '',
            'payee_id' => (int) $this->inPut('payeeId') ?? 0
        ];

        if ($data['type'] == Order::ORDER_TYPE_MERGE) {
            $totalAmount = $data['red_amount'] + $data['reward_amount'];
            if (Utils::compareMath($totalAmount, $data['amount'])) {
                $this->outPut(ResponseCode::INVALID_PARAMETER, '订单金额错误！', '');
            }
        }

        try {
            app('validator')->validate($data, [
                'amount'        => 'required_if:type,' . Order::ORDER_TYPE_REWARD . '|numeric|min:0.01',
                'is_anonymous'    => 'required|int|in:0,1',
                'type'   => 'required|int|min:1|max:11',
                'thread_id'     => 'required_if:type,' . Order::ORDER_TYPE_REWARD . ',' . Order::ORDER_TYPE_THREAD . '|int'
            ]);
        } catch (\Exception $e) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '', $e->getMessage());
        }

        $orderType = $data['type'];
        $order_zero_amount_allowed = false; //是否允许金额为0

        switch ($orderType) {
            // 注册订单
            case Order::ORDER_TYPE_REGISTER:
                $payeeId = Order::REGISTER_PAYEE_ID;
                $amount = sprintf('%.2f', (float) $this->settings->get('site_price'));

                // 查询是否有上级邀请 -> 注册分成
                if ($this->user->isAllowScale(Order::ORDER_TYPE_REGISTER)) {
                    $be_scale = $this->user->userDistribution->be_scale;
                }
                break;

            // 主题打赏订单
            case Order::ORDER_TYPE_REWARD:
                /** @var Thread $thread */
                $thread = Thread::query()
                    ->where('id', $data['thread_id'])
                    ->where('price', 0)                // 免费主题才可以打赏
                    ->where('is_approved', Thread::APPROVED)
                    ->whereNull('deleted_at')
                    ->first();

                if ($thread) {

                    $payeeId = $thread->user_id;
                    $amount = sprintf('%.2f', $data['amount']);

                    // 判断权限是否可以邀请用户分成，查询收款人是否有上级邀请
                    if ($thread->user->can('other.canInviteUserScale') && $thread->user->isAllowScale(Order::ORDER_TYPE_REWARD)) {
                        $be_scale = $thread->user->userDistribution->be_scale;
                    }
                } else {
                    throw new Exception(trans('order.order_post_not_found'));
                }
                break;

            // 付费主题订单
            case Order::ORDER_TYPE_THREAD:
                // 根据主题 id 查询非自己的付费主题
                /** @var Thread $thread */
                $thread = Thread::query()
                    ->where('id', $data['thread_id'])
                    ->where('user_id', '<>', $this->user->id)
                    ->where('price', '>', 0)
                    ->where('is_approved', Thread::APPROVED)
                    ->whereNull('deleted_at')
                    ->first();

                // 根据主题 id 查询是否已付过费
                $order = Order::query()
                    ->where('thread_id', $data['thread_id'])
                    ->where('user_id', $this->user->id)
                    ->where('status', Order::ORDER_STATUS_PAID)
                    ->where('type', Order::ORDER_TYPE_THREAD)
                    ->exists();

                // 主题存在且未付过费
                if ($thread && ! $order) {
                    $payeeId = $thread->user_id;
                    $amount = $thread->price;

                    // 查询收款人是否有上级邀请
                    if ($thread->user->can('other.canInviteUserScale') && $thread->user->isAllowScale(Order::ORDER_TYPE_THREAD)) {
                        $be_scale = $thread->user->userDistribution->be_scale;
                    }
                } else {
                    throw new Exception(trans('order.order_post_not_found'));
                }
                break;
            // 付费用户组
            case Order::ORDER_TYPE_GROUP:
                $order_zero_amount_allowed = true;
                $group_id = $data['group_id'];
                if (in_array($group_id, Group::PRESET_GROUPS)) {
                    throw new Exception(trans('order.order_group_forbidden'));
                }

                if (!$this->settings->get('site_pay_group_close')) {
                    //权限购买开关未开启
                    throw new Exception(trans('order.order_pay_group_closed'));
                }

                /** @var Group $group */
                $group = Group::query()->find($group_id);
                if (
                    isset($group->days)
                    && $group->days > 0
                    && $group->is_paid == Group::IS_PAID
                    && $group->fee > 0
                ) {
                    $payeeId = Order::REGISTER_PAYEE_ID;
                    $amount = $group->fee;
                } else {
                    throw new Exception(trans('order.order_group_error'));
                }
                break;
            // 问答提问支付
            case Order::ORDER_TYPE_QUESTION:
                // 创建订单
                $amount = sprintf('%.2f', $data['amount']); // 设置订单问答价格
                $payeeId = $data['payee_id']; // 设置收款人 (回答人)

                break;
            // 问答围观付费
            case Order::ORDER_TYPE_ONLOOKER:
                /** @var Thread $thread */
                $thread = Thread::query()
                    ->where('id', $data['thread_id'])
                    ->where('price', 0)  // 问答的帖子价格是0
                    ->where('is_approved', Thread::APPROVED)
                    ->where('type', Thread::TYPE_OF_QUESTION)
                    ->whereNull('deleted_at')
                    ->first();

                if ($thread && $thread->question) {
                    // 查询是否已经围观过，一个用户只允许围观一次
                    if ($thread->onlookerState($this->user)->exists()) {
                        throw new Exception(trans('order.order_question_onlooker_seen'));
                    }
                    // 判断该问答是否允许围观
                    if (! $thread->question->is_onlooker) {
                        throw new Exception(trans('order.order_question_onlooker_reject'));
                    }
                    // 判断该问题是否已被回答才能围观
                    if ($thread->question->is_answer != Question::TYPE_OF_ANSWERED) {
                        throw new Exception(trans('order.order_question_onlooker_unanswered'));
                    }

                    // 主题的围观单价
                    $amount = $thread->question->onlooker_unit_price; // 主题的围观单价

                    // 设置收款人
                    $payeeId = $thread->user_id; // 提问人
                    $thirdPartyId = $thread->question->be_user_id; // 第三者收益人（回答人）
                } else {
                    throw new Exception(trans('order.order_post_not_found'));
                }
                break;
            //付费附件
            case Order::ORDER_TYPE_ATTACHMENT:
                /** @var Thread $thread */
                $thread = Thread::query()
                    ->where('id', $data['thread_id'])
                    ->where('user_id', '<>', $this->user->id)
                    ->where('attachment_price', '>', 0)
                    ->where('is_approved', Thread::APPROVED)
                    ->whereNull('deleted_at')
                    ->first();

                // 根据主题 id 查询是否已付过费
                $order = Order::query()
                    ->where('thread_id', $data['thread_id'])
                    ->where('user_id', $this->user->id)
                    ->where('status', Order::ORDER_STATUS_PAID)
                    ->where('type', Order::ORDER_TYPE_ATTACHMENT)
                    ->exists();

                if ($thread && ! $order && $thread->attachment_price > 0) {
                    $payeeId = $thread->user_id;
                    $amount = $thread->attachment_price;

                    // 付费附件也是用主题的分成权限。查询收款人是否有上级邀请
                    if ($thread->user->can('other.canInviteUserScale') && $thread->user->isAllowScale(Order::ORDER_TYPE_THREAD)) {
                        $be_scale = $thread->user->userDistribution->be_scale;
                    }
                } else {
                    throw new Exception(trans('order.order_thread_attachment_error'));
                }
                break;
            // 站点续费
            case Order::ORDER_TYPE_RENEW:
                $payeeId = Order::REGISTER_PAYEE_ID;
                $amount = sprintf('%.2f', (float) $this->settings->get('site_price'));

                break;

            // 红包支出
            case Order::ORDER_TYPE_REDPACKET:
                // 创建订单
                $amount = sprintf('%.2f', $data['amount']); // 设置红包价格
                $payeeId = 0;

                break;

            // 悬赏支出
            case Order::ORDER_TYPE_QUESTION_REWARD:
                // 创建订单
                $amount = sprintf('%.2f', $data['amount']); // 设置悬赏价格
                $payeeId = 0;
                break;

            // 合并订单支出
            case Order::ORDER_TYPE_MERGE:
                // 创建订单
                $amount = sprintf('%.2f', $data['amount']); // 设置红包+悬赏价格
                $payeeId = 0;
                break;

            default:
                $this->info('参数type枚举错误,传参枚举type:({$orderType}),用户id:{$this->user->id}');
                throw new Exception(trans('order.order_type_error'));
        }

        // 订单金额需检查
        if (($amount == 0 && ! $order_zero_amount_allowed) || $amount < 0) {
            $this->info('参数金额错误,用户id:' . $this->user->id);
            throw new Exception(trans('order.order_amount_error'));
        }

        // 支付编号
        $payment_sn = $this->getPaymentSn();

        // 支付通知
        $pay_notify             = new PayNotify();
        $pay_notify->payment_sn = $payment_sn;
        $pay_notify->user_id    = $this->user->id;

        $order                  = new Order();
        $order->payment_sn      = $payment_sn;
        $order->order_sn        = $this->getOrderSn();
        $order->amount          = $amount;
        $order->be_scale        = $be_scale ?? 0;
        $order->third_party_id  = $thirdPartyId ?? 0; // 第三者收益人
        $order->user_id         = $this->user->id;
        $order->type            = $orderType;
        $order->thread_id       = isset($thread) ? $thread->id : null;
        $order->group_id        = isset($group_id) ? $group_id : null;
        $order->payee_id        = $payeeId;
        $order->is_anonymous    = $data['is_anonymous'];
        $order->status          = 0;

        if ($orderType == Order::ORDER_TYPE_MERGE) {
            $redAmount = sprintf('%.2f', $data['red_amount']);
            $rewardAmount = sprintf('%.2f', $data['reward_amount']);
            $redResult = $this->insertOrderChildren($order, $redAmount, Order::ORDER_TYPE_REDPACKET);
            $rewardResult = $this->insertOrderChildren($order, $rewardAmount, Order::ORDER_TYPE_QUESTION_REWARD);
        }

        $db = $this->getDB();
        $db->beginTransaction();
        try {
           if ($amount == 0 && $order_zero_amount_allowed) {
                //用户组0付费
                $order->status = 1;
            }
            $pay_notify->save();    // 保存通知数据
            $order->save();
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            $this->info('createOrder_error_' . $this->user->id, $e->getMessage());
            $this->outPut(ResponseCode::DB_ERROR, $e->getMessage());
        }

        $this->outPut(ResponseCode::SUCCESS, '', $this->camelData($order));
    }

    /**
     * 生成支付编号
     * @return string  18位字符串
     */
    public function getPaymentSn()
    {
        return date('Ymd')
        . str_pad(strval(mt_rand(1, 99)), 2, '0', STR_PAD_LEFT)
        . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    /**
     * 生成订单编号
     * @return string 22位字符串
     */
    public function getOrderSn()
    {
        return date('YmdHis', time()) . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    public function insertOrderChildren($order, $childAmount, $type)
    {
        $orderChildren = new OrderChildren();
        $orderChildren->order_sn = $order->order_sn;
        $orderChildren->type = $type;
        $orderChildren->status = $order->status;
        $orderChildren->amount = $childAmount;
        $orderChildren->thread_id = $order->thread_id ?? 0;

        $db = $this->getDB();
        $db->beginTransaction();
        try {
            $orderChildren->save();
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            $this->info('createOrderChildren_error_' . $this->user->id, $e->getMessage());
            $this->outPut(ResponseCode::DB_ERROR, $e->getMessage());
        }
    }
}
