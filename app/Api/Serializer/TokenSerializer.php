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

namespace App\Api\Serializer;

use App\Common\CacheKey;
use App\Common\Statistics;
use App\Models\User;
use Discuz\Api\Serializer\AbstractSerializer;
use Discuz\Common\PubEnum;
use Discuz\Common\Utils;

class TokenSerializer extends AbstractSerializer
{
    protected $type = 'token';

    protected static $user;

    public static function setUser($user)
    {
        static::$user = $user;
    }

    public static function getUser()
    {
        return static::$user;
    }

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param object|array $model
     * @return array
     */
    protected function getDefaultAttributes($model)
    {
        $build = [
            'token_type' => $model->token_type,
            'expires_in' => $model->expires_in,
            'access_token' => $model->access_token,
            'refresh_token' => $model->refresh_token,
            'status'=>$this->getUserStatus()
        ];
        $userId = $this->getId($model);
        $cache = app('cache');
        $cacheKey = CacheKey::NEW_USER_LOGIN . $userId;
        $data = $cache->get($cacheKey);
        if (!empty($data)) {
            $build['new_user'] = true;
            $cache->put($cacheKey,$data,10);
        }
        if($build['status'] == User::STATUS_NORMAL){
            $this->localStatistics();
        }
        return $build;
    }

    private function localStatistics()
    {
        $platform = Utils::requestFrom();
        $t = date('Y-m-d');
        $keyPc = 'login_pc_count:' . $t;
        $keyH5 = 'login_h5_count:' . $t;
        $keyMp = 'login_mp_count:' . $t;
        switch ($platform) {
            case PubEnum::PC:
                $pcNum = Statistics::get($keyPc);
                !$pcNum && $pcNum = 0;
                Statistics::set($keyPc, ++$pcNum);
                break;
            case PubEnum::H5:
                $h5Num = Statistics::get($keyH5);
                !$h5Num && $h5Num = 0;
                Statistics::set($keyH5, ++$h5Num);
                break;
            case PubEnum::MinProgram:
                $mpNum = Statistics::get($keyMp);
                !$mpNum && $mpNum = 0;
                Statistics::set($keyMp, ++$mpNum);
                break;
            default:
                break;
        }
    }

    public function getUserStatus()
    {
        $user = self::getUser();
        $status = User::STATUS_NORMAL;
        if (!empty($user)) {
            if (isset($user['status'])) {
                $status = $user['status'];
            }
        }
        return $status;
    }

    public function getId($model)
    {
        if (property_exists($model, 'pc_login')) {
            return $model->user_id;
        }

        return static::$user->id;
    }

    public function users($model)
    {
        return $this->hasOne(['users' => static::$user], UserProfileSerializer::class);
    }
}
