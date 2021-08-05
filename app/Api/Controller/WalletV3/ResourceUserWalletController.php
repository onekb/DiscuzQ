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
use App\Models\UserWallet;
use App\Repositories\UserWalletRepository;
use App\Settings\SettingsRepository;
use Discuz\Base\DzqController;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\PermissionDeniedException;

class ResourceUserWalletController extends DzqController
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

    // 权限检查，是否为注册用户
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
        $actor = $this->user;
        $user_id = $actor->id;
        if(!$user_id){
            $this->outPut(ResponseCode::INVALID_PARAMETER);
        }

        $query = User::query();
        $groupData = $query->where('id',$user_id)->first();
        if(empty($groupData)){
            $this->outPut(ResponseCode::INVALID_PARAMETER, 'ID为'.$user_id.'用户不存在');
        }
        $data = UserWallet::where('user_id', $user_id)->first();
        $data->cash_tax_ratio = $this->setting->get('cash_rate', 'cash', 0);
        $data = $this->camelData($data);
        return $this->outPut(ResponseCode::SUCCESS,'', $data);
    }


}
