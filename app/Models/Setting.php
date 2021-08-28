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

use App\Common\CacheKey;
use Discuz\Base\DzqCache;
use Discuz\Base\DzqModel;

/**
 * @property string $key
 * @property string $value
 * @property string $tag
 */
class Setting extends DzqModel
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = ['key', 'value', 'tag'];

    /**
     * {@inheritdoc}
     */
    protected $primaryKey = ['key', 'tag'];

    /**
     * {@inheritdoc}
     */
    protected $keyType = 'string';

    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    public $timestamps = false;

    public static $encrypt;

    const DIGIEAL = 0; //密码类型必须有数字
    const LOWER_CASE_LETTERS = 1; //密码类型必须有小写字母
    const SYMBOL = 2; //密码类型必须有符号
    const UPPERCASE_LETTER = 3; //密码类型必须有大写字母
    /**
     * 开启云服务有关联动选项
     * @var array
     */
    public static $linkage = [
        'qcloud_cms_image',             //图片内容安全
        'qcloud_cms_text',              //文本内容安全
        'qcloud_sms',                   //短信
        'qcloud_faceid',                //实名认证
        'qcloud_cos',                   //对象存储
        'qcloud_vod',                   //云点播
        'qcloud_captcha',               //验证码
    ];

    /**
     * 需要加密的数据字段
     *
     * @var array
     */
    public static $checkEncrypt = [
        'app_id',
        'app_secret',
        'api_key',
        'offiaccount_app_id',
        'offiaccount_app_secret',
        'miniprogram_app_id',
        'miniprogram_app_secret',
        'oplatform_app_id',
        'oplatform_app_secret',
        'qcloud_secret_id',
        'qcloud_secret_key',
        'qcloud_sms_app_id',
        'qcloud_sms_app_key',
        'qcloud_sms_template_id',
        'qcloud_sms_sign',
        'qcloud_captcha_app_id',
        'qcloud_captcha_secret_key',
        'qcloud_vod_url_key',
        'offiaccount_server_config_token',
        'uc_center_key',
    ];

    /**
     * 全局中的功能设置权限 -- 关联控制角色中的权限
     */
    public static $global_permission = [
        'site_create_thread0' => ['createThread.0'],
        'site_create_thread1' => ['createThread.1'],
        'site_create_thread2' => ['createThread.2'],
        'site_create_thread3' => ['createThread.3'],
        'site_create_thread4' => ['createThread.4'],
        'site_create_thread5' => ['createThread.5'],
        'site_create_thread6' => ['createThread.6'],
        'site_can_reward' => ['switch.thread.canBeReward', 'thread.canBeReward']
    ];

    /**
     * Set the encrypt.
     *
     * @param $encrypt
     */
    public static function setEncrypt($encrypt)
    {
        self::$encrypt = $encrypt;
    }

    /**
     * each data decrypt
     */
    public function existDecrypt()
    {
        if (in_array($this->key, self::$checkEncrypt)) {
            return;
        }
    }

    /**
     * 解密数据
     *
     * @param $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        if (in_array($this->key, self::$checkEncrypt)) {
            $value = empty($value) ? $value : static::$encrypt->decrypt($value);
        }

        return $value;
    }

    /**
     * 加密数据
     * (insert 和 update 不操作 Eloquent)
     *
     * @param $key
     * @param $value
     */
    public static function setValue($key, &$value)
    {
        if (in_array($key, self::$checkEncrypt)) {
            $value = static::$encrypt->encrypt($value);
        }
    }

    public static function isMiniProgramVideoOn()
    {
        $request = app('request');
        $headers = $request->getHeaders();
        $server = $request->getServerParams();
        $headersStr = strtolower(json_encode($headers, 256));
        $serverStr = strtolower(json_encode($server, 256));

        if (strstr($serverStr, 'miniprogram') || strstr($headersStr, 'miniprogram') ||
            strstr($headersStr, 'compress')) {
            $settings = Setting::query()->where(['key' => 'miniprogram_video', 'tag' => 'wx_miniprogram'])->first();
            if (!$settings->value) {
                return false;
            }
        }

        return true;
    }


    /*
 * 更新value值
 */
    public static function modifyValue($key, $value, $tag = 'default')
    {
        return Setting::query()->where('key', $key)->where('tag', $tag)->update(['value' => $value]);
    }


    /*
  * 获取value参数值
  */
    public static function getValue($key, $tag = '', $value = '')
    {
        if ($key) {
            $settings = Setting::query()->where('key', $key);
        }
        if ($tag) {
            $settings = $settings->where('tag', $tag);
        }
        if ($value) {
            $settings = $settings->where('value', $value);
        }
        return $settings->value('value');
    }

    protected function clearCache()
    {
        DzqCache::delKey(CacheKey::SETTINGS);
    }
}
