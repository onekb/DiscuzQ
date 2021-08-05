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

use App\Common\ResponseCode;
use App\Models\User;
use App\Repositories\UserWalletRepository;
use App\Settings\SettingsRepository;
use Discuz\Base\DzqController;
use App\Repositories\UserRepository;

class ResourceUserWalletAdminController extends DzqController
{

    public $wallet;
    public $setting;

    public $include = [
    ];

    public function __construct(UserWalletRepository $wallet, SettingsRepository $setting)
    {
        $this->wallet = $wallet;
        $this->setting = $setting;
    }

    // 权限检查，是否为管理员
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $userRepo->canUserWallet($this->user);
    }

    public function main()
    {
        $user_id = $this->inPut('uid');

        if (empty($user_id)){
            $this->outPut(ResponseCode::INVALID_PARAMETER, 'ID为'.$user_id.'用户不存在');
        }

        $data = $this->wallet->findOrFail($user_id, $this->user);

        $dat = User::query()->where('id','=', $user_id)->pluck('username')->toArray();

        $data->username = $dat[0];

        $data->cash_tax_ratio = $this->setting->get('cash_rate', 'cash', 0);

        $data = $this->camelData($data);

        return $this->outPut(ResponseCode::SUCCESS,'', $data);

    }


}