<?php
/**
 * Copyright (C) 2021 Tencent Cloud.
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

namespace App\Api\Controller\WalletV3;


use App\Api\Serializer\UserWalletCashSerializer;

use App\Common\ResponseCode;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\UserWalletCashRepository;
use Discuz\Base\DzqController;
use Discuz\Http\UrlGenerator;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tobscure\JsonApi\Parameters;

class ListUserWalletCashController extends DzqController
{
    protected $bus;
    protected $url;
    protected $cash;
    protected $total;

    public $sort = [
        'created_at'    =>  'desc'
    ];

    public $sortFields = [
        'created_at',
        'updated_at'
    ];

    public $optionalInclude = [
        'user',
        'userWallet',
        'wechat'
    ];

    public function __construct(Dispatcher $bus, UrlGenerator $url, UserWalletCashRepository $cash)
    {
        $this->bus = $bus;
        $this->url = $url;
        $this->cash = $cash;
    }

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        $actor = $this->user;
        if ($actor->isGuest()) {
            $this->outPut(ResponseCode::JUMP_TO_LOGIN);
        }
        return true;
    }

    public function main()
    {
        $user_wallet_serializer = $this->app->make(UserWalletCashSerializer::class);
        $user_wallet_serializer->setRequest($this->request);
        $filter     = $this->inPut('filter') ?: [];
        $page       = $this->inPut('page') ?: 1;
        $perPage    = $this->inPut('perPage') ?: 5;
        $sort = (new Parameters($this->request->getQueryParams()))->getSort($this->sortFields) ?: $this->sort;

        $cash_records = $this->getCashRecords($this->user, $filter, $perPage, $page, $sort);
        $data = $this->camelData($cash_records);

        $data = $this->filterData($data);

        return $this->outPut(ResponseCode::SUCCESS,'', $data);
    }

    private function getCashRecords($actor, $filter, $perPage = 0, $page = 0, $sort = [])
    {
        $cash_user       = $actor->id; //提现用户
        $cash_sn         = Arr::get($filter, 'cashSn'); //提现流水号
        $cash_status     = Arr::get($filter, 'cashStatus'); //提现状态
        $cash_username   = Arr::get($filter, 'username'); //提现人
        $cash_type       = Arr::get($filter, 'cashType'); //提现方式
        $cash_mobile     = Arr::get($filter, 'cashMobile'); //提现到的手机号码
        $cash_start_time = Arr::get($filter, 'startTime'); //申请时间范围：开始
        $cash_end_time   = Arr::get($filter, 'endTime'); //申请时间范围：结束

        $query = $this->cash->query();
        $query->when($cash_user, function ($query) use ($cash_user) {
            $query->where('user_id', $cash_user);
        });
        $query->when($cash_sn, function ($query) use ($cash_sn) {
            $query->where('cash_sn', $cash_sn);
        });

        $query->when(!is_null($cash_status), function ($query) use ($cash_status) {
            $query->whereIn('cash_status', (array) $cash_status);
        });

        $query->when(!is_null($cash_type), function ($query) use ($cash_type) {
            $query->where('cash_type', $cash_type);
        });
        $query->when($cash_mobile, function ($query) use ($cash_mobile) {
            $query->where('cash_mobile', $cash_mobile);
        });

        $query->when($cash_start_time, function ($query) use ($cash_start_time) {
            $query->where('created_at', '>=', $cash_start_time);
        });
        $query->when($cash_end_time, function ($query) use ($cash_end_time) {
            $query->where('created_at', '<=', $cash_end_time);
        });
        $query->when($cash_username, function ($query) use ($cash_username) {
            $query->whereIn('user_wallet_cash.user_id', User::where('users.username', $cash_username)->select('id', 'username')->get());
        });
        foreach ((array) $sort as $field => $order) {
            $query->orderBy(Str::snake($field), $order);
        }
        return $this->pagination($page, $perPage, $query);

    }

    public function filterData($data){
        foreach ($data['pageData'] as $key => $val) {
            if(empty($val['cash_type']))    $val['tradeNo'] = '线下打款';
            $pageData = [
                'tradeNo'           =>  !empty($val['tradeNo']) ? $val['tradeNo'] : 0,
                'remark'            =>  !empty($val['remark']) ? $val['remark'] : '',
                'cashApplyAmount'   =>  !empty($val['cashApplyAmount']) ? $val['cashApplyAmount'] : 0,
                'tradeTime'         =>  !empty($val['tradeTime']) ? $val['tradeTime'] : 0,
                'cashStatus'        =>  !empty($val['cashStatus']) ? $val['cashStatus'] : 0,

            ];
            $data['pageData'][$key] =  $pageData;
        }

        return $data;
    }


}
