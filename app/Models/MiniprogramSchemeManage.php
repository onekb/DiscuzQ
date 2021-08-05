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

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $mini_app_id
 * @property string $scheme
 * @property int    $expired_at
 * @property int    $created_at
 * Class MiniprogramSchemeManage
 * @package App\Models
 */
class MiniprogramSchemeManage extends Model
{

    public $timestamps = false;

    const UPDATED_AT = null;

    /**
     * {@inheritdoc}
     */
    protected $table = 'miniprogram_scheme_manage';

    /**
     * {@inheritdoc}
     */
    protected $fillable = ['mini_app_id', 'scheme', 'expired_at'];


    /**
     * Create a new scheme record
     * @param $accessToken
     * @param $appId
     * @param $expireIn
     * @return static
     */
    public static function createRecord($scheme, $appId, $expired)
    {
        $miniprogramSchemeManage = new static;
        $miniprogramSchemeManage->mini_app_id = $appId;
        $miniprogramSchemeManage->scheme = $scheme;
        $miniprogramSchemeManage->expired_at = $expired;
        $miniprogramSchemeManage->created_at = Carbon::now()->getTimestamp();
        $miniprogramSchemeManage->save();
        return $miniprogramSchemeManage;
    }

    /**
     * 查询最新的一条记录
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getLastRecord()
    {
        return self::query()->orderBy('id', 'desc')->limit(1)->first();
    }

    /**
     * 删除记录
     * @param $id
     * @return mixed
     */
    public static function deleteById($id)
    {
        return self::query()->where('id', $id)->delete();
    }

}
