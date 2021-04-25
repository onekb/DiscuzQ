<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *   http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Models;

use Discuz\Wechat\EasyWechatTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use s9e\RegexpBuilder\Builder;

/**
 * Class NotificationTpl
 *
 * @property int $id
 * @property string $notice_id
 * @property int $status
 * @property int $type
 * @property int $type_name
 * @property string $title
 * @property string $content
 * @property string $vars
 * @property string $template_id
 * @property string $first_data
 * @property string $keywords_data
 * @property string $remark_data
 * @property string $color
 * @property string $redirect_type
 * @property string $redirect_url
 * @property string $page_path
 * @property int $is_error
 * @property string $error_msg
 */
class NotificationTpl extends Model
{
    use EasyWechatTrait;

    const OPEN = 1;

    const SYSTEM_NOTICE            = 0; // 数据库（系统）通知
    const WECHAT_NOTICE            = 1; // 微信通知
    const SMS_NOTICE               = 2; // 短信通知
    const ENTERPRISE_WECHAT_NOTICE = 3; // 企业微信通知
    const MINI_PROGRAM_NOTICE      = 4; // 小程序通知

    /**
     * 跳转类型：0无跳转 1跳转H5 2跳转小程序
     */
    const REDIRECT_TYPE_TO_NO          = 0;
    const REDIRECT_TYPE_TO_H5          = 1;
    const REDIRECT_TYPE_TO_MINIPROGRAM = 2;

    public $timestamps = false;

    public $table = 'notification_tpls';

    protected $fillable = [
        'notice_id',
        'status',
        'type',
        'type_name',
        'title',
        'content',
        'vars',
        'template_id',
        'first_data',
        'keywords_data',
        'remark_data',
        'color',
        'redirect_type',
        'redirect_url',
        'page_path',
        'is_error',
        'error_msg',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'color'     => 'array',
        'error_msg' => 'array',
    ];

    /**
     * 枚举 - type
     * 通知类型: 0系统 1微信 2短信 3企业微信 4小程序通知
     *
     * @var array
     */
    protected static $status = [
        'database'         => 0,
        'wechat'           => 1,
        'sms'              => 2,
        'enterpriseWeChat' => 3,
        'miniProgram'      => 4,
    ];

    protected static $typeName = [
        0 => '系统',
        1 => '微信',
        2 => '短信',
        3 => '企业微信',
        4 => '小程序通知',
    ];

    /**
     * Create a new user notification_tpl
     *
     * @param array $attributes
     * @return static
     */
    public static function build(array $attributes)
    {
        $distribute = new static;

        $distribute->fill($attributes);

        return $distribute;
    }

    /**
     * 根据 值/类型 获取对应值
     *
     * @param $mixed
     * @return false|int|string
     */
    public static function enumType($mixed)
    {
        $arr = static::$status;

        if (is_numeric($mixed)) {
            return array_search($mixed, $arr);
        }

        return $arr[$mixed];
    }

    /**
     * 获取对应 type 名称
     *
     * @param int $type
     * @param string $suffix
     * @return string
     */
    public static function enumTypeName(int $type, string $suffix = '') : string
    {
        $typeName = static::$typeName;

        if (isset($typeName[$type])) {
            return $typeName[$type] . $suffix;
        }

        return '';
    }

    /**
     * 模板发送失败，写入错误
     *
     * @param NotificationTpl $notificationData
     * @param int $errCode 错误编号
     * @param string $errMsg 错误信息
     * @param array $sendBuild 发送的数据
     * @return bool
     */
    public static function writeError(NotificationTpl $notificationData, $errCode, $errMsg, $sendBuild = []) : bool
    {
        /**
         * 微信小程序
         *
         * 43101 用户拒绝接受消息 并不是配置报错
         *
         * @URL err_code 的合法值 https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html
         */
        if ($notificationData->type == NotificationTpl::MINI_PROGRAM_NOTICE) {
            if (in_array((int) $errCode, [43101])) {
                return false;
            }
        }

        /**
         * 短信
         *
         * 1023 单个手机号30秒内下发短信条数超过设定的上限
         * 1024 单个手机号1小时内下发短信条数超过设定的上限
         * 1025 单个手机号日下发短信条数超过设定的上限
         * 1026	单个手机号下发相同内容超过设定的上限
         *
         * @URL err_code 的合法值 https://cloud.tencent.com/document/product/382/49316
         */
        if ($notificationData->type == NotificationTpl::SMS_NOTICE) {
            if (in_array((int) $errCode, [1023, 1024, 1025, 1026])) {
                return false;
            }
        }

        // 查询当前是否已经存有错误
        if ($notificationData->is_error) {
            /** @var Builder $buildError */
            $buildError = $notificationData->error_msg;

            // 检测是否已经存在过相同 error code，不存在则拼接多个 error code
            $errCodes = explode('、', $buildError['err_code']);
            if (! in_array($errCode, $errCodes)) {
                $buildError['err_code'] .= '、' . $errCode;
                $buildError['err_msg'] .= ' #@@@# ' . $errMsg;
            } else {
                return true;
            }
        } else {
            /** @var Builder $buildError */
            $buildError = [
                'id'         => $notificationData->id,
                'type_name'  => $notificationData->type_name,
                'send_build' => $sendBuild,
                'err_code'   => $errCode,
                'err_msg'    => $errMsg ?: 'unknown mistake',
            ];

            $notificationData->is_error = 1;
        }

        $notificationData->error_msg = $buildError;

        $notificationData->save();

        return true;
    }

    /**
     * 微信通知 - 数据格式
     *
     * @param $arr
     * @return false|string
     */
    public static function getWechatFormat($arr)
    {
        $result = [
            'data'         => [
                'first'    => [
                    'value' => $arr['first'] ?? '',
                    'color' => $arr['color']['first_color'] ?? '#173177',
                ],
                'keyword1' => [
                    'value' => $arr['keyword1'] ?? '',
                    'color' => $arr['color']['keyword1_color'] ?? '#173177',
                ],
                'keyword2' => [
                    'value' => $arr['keyword2'] ?? '',
                    'color' => $arr['color']['keyword2_color'] ?? '#173177',
                ],
                'remark'   => [
                    'value' => $arr['remark'] ?? '',
                    'color' => $arr['color']['remark_color'] ?? '#173177',
                ],
            ],
            'redirect_url' => $arr['redirect_url'] ?? '',
        ];

        /**
         * 公众号限制 (keyword最少2个 最多是5个)
         */
        for ($i = 3; $i < 5; $i++) {
            $keyword = 'keyword' . $i;
            if (array_key_exists($keyword, $arr)) {
                $result['data'][$keyword] = [
                    'value' => $arr[$keyword] ?? '',
                    'color' => $arr['color']['keyword' . $i . '_color'] ?? '#173177',
                ];
            } else {
                break;
            }
        }

        return json_encode($result);
    }

    /**
     * 短信通知 - 数据格式
     *
     * @param $arr
     * @return Collection
     */
    public static function getSmsFormat($arr)
    {
        $keywords = $arr['keywords'] ?? '';

        /**
         * 短信每个变量的长度限制12个字符。如果要取消限制，需要申请企业认证
         *
         * @url https://cloud.tencent.com/developer/ask/189879
         */
        return collect($keywords)->map(function ($item) {
            if (Str::length($item) > 12) {
                // 截取字符串
                $item = Str::of($item)->substr(0, 9) . '...';
            }

            return $item;
        });
    }

    /**
     * 小程序通知 - 数据格式
     *
     * @param $arr
     * @param array $keys
     * @return array
     */
    public static function getMiniProgramContent($arr, array $keys) : array
    {
        $arr = array_values($arr);
        $build = [];
        foreach ($keys as $k => $v) {
            $build[$v] = $arr[$k] ?? '';
        }

        $data = collect($build)->map(function ($item, $key) {
            return $result[$key] = ['value' => $item];
        });

        return $data->toArray();
    }

}
