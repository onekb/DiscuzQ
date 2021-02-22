<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Console\Commands\Upgrades;

use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use App\Settings\SettingsRepository;
use Carbon\Carbon;
use Discuz\Console\AbstractCommand;

class InitOrdersExpiredAt extends AbstractCommand
{
    protected $signature = 'upgrade:ordersExpiredAt';

    protected $description = 'Initialize orders expired at for user pay';

    protected $orders;

    protected $users;

    protected $settings;

    public function __construct(OrderRepository $orders, UserRepository $users, SettingsRepository $settings)
    {
        parent::__construct();

        $this->orders = $orders;
        $this->users = $users;
        $this->settings = $settings;
    }

    public function handle()
    {
        $users = $this->users->query()
            ->select(['id', 'joined_at', 'expired_at'])
            ->get();
        $site_expire = $this->settings->get('site_expire');

        /** @var User $user */
        foreach ($users as $user) {
            //非付费模式
            /** @var Order $order */
            $order = $this->orders->query()
                ->where('user_id', $user->id)
                ->where('type', Order::ORDER_TYPE_REGISTER)
                ->where('status', Order::ORDER_STATUS_PAID)
                ->first();
            if ($order) {
                if ($user->expired_at > Carbon::now()->toDateTimeString()) {
                    $order->expired_at = $user->expired_at;
                } else {
                    //当前无过期时间 且用户付费订单改为已付费的时间=用户付费过期时间，设置订单没有过期时间
                    if (!$site_expire && ($user->expired_at == $order->updated_at)) {
                        $order->expired_at = null;
                    }
                }
                $order->save();
            }
        }
        $this->info('success');
    }
}
