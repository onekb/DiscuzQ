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

namespace App\Common;

/**
 * 登录，授权通用工具
 * Class AuthUtils
 * @package App\Common
 */
class AuthUtils
{

    /**
     * 用户登录类型，用户名密码
     */
    const DEFAULT = 0;
    /**
     * 微信
     */
    const WECHAT = 1;
    /**
     * qq
     */
    const QQ = 2;
    /**
     * 手机号
     */
    const PHONE = 4;

    /**
     * 登录类型，倒序排
     * @return int[]
     */
    public static function getLoginTypeArr()
    {
        return [self::PHONE, self::QQ,  self::WECHAT, self::DEFAULT];
    }

    /**
     * 计算绑定类型组合值
     * @param int $phoneType
     * @param int $wechatType
     * @param int $qqType
     * @param int $other
     * @return int
     */
    public static function getCombinationBindType($phoneType = 0, $wechatType = 0, $qqType = 0, $other = 0)
    {
        $combinationBindType = 0;
        if($phoneType > 0) {
            $combinationBindType += $phoneType;
        }
        if($wechatType > 0 ) {
            $combinationBindType += $wechatType;
        }
        if($qqType > 0) {
            $combinationBindType += $qqType;
        }
        if($other >0) {
            $combinationBindType += $other;
        }
        return $combinationBindType;
    }

    /**
     * 根据组合绑定类型计算出，绑定了哪些类型
     * @param $combinationBindType
     * @return array
     */
    public static function getBindTypeArrByCombinationBindType($combinationBindType)
    {
        $loginTye = self::getLoginTypeArr();
        $bindTypeArr = [];
        foreach ($loginTye as $type) {
            if($type == 0) break;
            if($combinationBindType - $type >= 0) {
                $combinationBindType -= $type;
                array_push($bindTypeArr, $type);
                continue;
            }
        }
        return $bindTypeArr;
    }

    /**
     * 根据新组合的绑定类型计算出 bind_type 值
     * @param array $bindTypeArr
     * @return int
     */
    public static function getBindType($bindTypeArr)
    {
        $bindTypeArr = is_array($bindTypeArr) ? $bindTypeArr : [];
        $binType = array_sum($bindTypeArr);
        return $binType;
    }

}
