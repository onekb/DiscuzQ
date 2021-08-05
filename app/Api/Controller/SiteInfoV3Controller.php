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

namespace App\Api\Controller;

use App\Common\ResponseCode;
use App\Models\Category;
use App\Models\Order;
use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use App\Models\UserWalletCash;
use App\Models\Setting;
use App\Repositories\UserRepository;
use Discuz\Foundation\Application;
use Discuz\Foundation\Support\Decomposer;
use Discuz\Qcloud\QcloudTrait;
use Discuz\Base\DzqController;
use Exception;
use Illuminate\Support\Arr;
use Psr\Http\Message\ResponseInterface;

class SiteInfoV3Controller extends DzqController
{
    use QcloudTrait;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }


    public function main()
    {
        $decomposer = new Decomposer($this->app, $this->request);
        $port = $this->request->getUri()->getPort();
        $siteUrl = $this->request->getUri()->getScheme() . '://' . $this->request->getUri()->getHost().(in_array($port, [80, 443, null]) ? '' : ':'.$port);

        // 提现分成
        $cashCharge = UserWalletCash::query()->where('cash_status', UserWalletCash::STATUS_PAID)->sum('cash_charge');

        // 注册分成
        $amount = Order::query()->where('type', Order::ORDER_TYPE_REGISTER)->where('status', Order::ORDER_STATUS_PAID)->sum('amount');

        // 站长分成
        $masterAmount = Order::query()->where('status', Order::ORDER_STATUS_PAID)->sum('master_amount');

        // 待审核用户数
        $unapprovedUsers = User::where('status', 2)->count();

        // 待审核主题数
        $unapprovedThreads = Thread::where('is_approved', Thread::UNAPPROVED)
            ->where('is_draft', 0)->whereNull('deleted_at')->whereNotNull('user_id')->count();

        // 待审核回复数
        $unapprovedPosts = Post::where('is_approved', Post::UNAPPROVED)
            ->whereNull('deleted_at')->whereNotNull('user_id')->where('is_first', false)->count();

        // 待审核提申请现数
        $unapprovedMoneys = UserWalletCash::where('cash_status', UserWalletCash::STATUS_REVIEW)
                          ->join('users', 'user_wallet_cash.user_id', '=', 'users.id')
                          ->count();

        $data = [
            'url' => $siteUrl,
            'site_id' => Setting::getValue('site_id'),
            'site_name' => Setting::getValue('site_name'),
            'site_income' => (float) Order::query()->where('status', Order::ORDER_STATUS_PAID)->sum('amount'),
            'site_owner_income' => $cashCharge + $amount + $masterAmount,
            'threads' => Thread::query()->count(),
            'posts' => Post::query()->count(),
            'users' => User::query()->count(),
            'orders' => Order::query()->count(),
            'categories' => serialize(Category::all()->toArray())
        ];

        try {
            $this->report($data)->then(function (ResponseInterface $response) {
                $data = json_decode($response->getBody()->getContents(), true);
                Setting::modifyValue('site_id', Arr::get($data, 'site_id'));
                Setting::modifyValue('site_secret', Arr::get($data, 'site_secret'));
            })->wait();
        } catch (Exception $e) {
            return $this->outPut(ResponseCode::NET_ERROR,$e);
        }

        $dec = $this->camelData($decomposer->getSiteinfo());

        $data = [
            'unapprovedUsers' => $unapprovedUsers,
            'unapprovedThreads' => $unapprovedThreads,
            'unapprovedPosts' => $unapprovedPosts,
            'unapprovedMoneys' => $unapprovedMoneys,
        ];

        $build = [
               'siteinfo'=>$dec,
               'unapproved' =>$data
         ];

        return $this->outPut(ResponseCode::SUCCESS,'',$build);
    }

}
