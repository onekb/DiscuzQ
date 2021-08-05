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

namespace App\Api\Controller\SettingsV3;

use App\Common\CacheKey;
use App\Common\ResponseCode;
use App\Events\Setting\Saved;
use App\Events\Setting\Saving;
use App\Models\AdminActionLog;
use App\Models\Setting;
use App\Repositories\UserRepository;
use App\Validators\SetSettingValidator;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Base\DzqCache;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Qcloud\QcloudTrait;
use Discuz\Base\DzqController;
use Exception;
use Illuminate\Contracts\Events\Dispatcher as Events;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SetSettingsController extends DzqController
{
    public function suffixClearCache($user)
    {
        DzqCache::delKey(CacheKey::SETTINGS);
    }

    use QcloudTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if (!$this->user->isAdmin()) {
            throw new PermissionDeniedException('没有权限');
        }
        return true;
    }

    /**
     * @var Events
     */
    protected $events;
    /**
     * @var SetSettingValidator
     */
    protected $validator;

    protected $settings;

    /**
     * @param Events $events
     * @param SetSettingValidator $validator
     */
    public function __construct(Events $events, SettingsRepository $settings, SetSettingValidator $validator)
    {
        $this->events = $events;
        $this->settings = $settings;
        $this->validator = $validator;

    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws PermissionDeniedException
     * @throws Exception
     */
    public function main()
    {
        $actor = $this->user;
        $user_id = $actor->id;

        // 转换为以 tag + key 为键的集合，即可去重又方便取用
        $settings = collect($this->inPut('data'))
            ->map(function ($item) {
                $item['tag'] = $item['tag'] ?? 'default';
                return $item;
            })
            ->keyBy(function ($item) {
                return $item['tag'] . '_' . $item['key'];
            });


        /**
         * TODO: 将不同功能的设置放到监听器中验证，不要全写在 SetSettingValidator
         * @example ChangeSiteMode::class
         * @deprecated SetSettingValidator::class（建议整改后废弃）
         */
        $this->events->dispatch(new Saving($settings));

        // 分成比例检查
        $siteAuthorScale = $settings->pull('default_site_author_scale');
        $siteMasterScale = $settings->pull('default_site_master_scale');

        // 只要传了其中一个，就检查分成比例相加是否为 10
        if ($siteAuthorScale || $siteMasterScale) {
            $siteAuthorScale = abs(Arr::get($siteAuthorScale, 'value', 0));
            $siteMasterScale = abs(Arr::get($siteMasterScale, 'value', 0));
            $sum = $siteAuthorScale + $siteMasterScale;

            if ($sum === 10) {
                $this->setSiteScale($siteAuthorScale, $siteMasterScale, $settings);
            } else {
                return $this->outPut(ResponseCode::INVALID_PARAMETER, 'scale_sum_not_10');
            }
        }

        // 扩展名统一改为小写
        $settings->transform(function ($item, $key) {
            $extArr = ['default_support_img_ext', 'default_support_file_ext', 'qcloud_qcloud_vod_ext'];
            if (in_array($key, $extArr)) {
                $item['value'] = strtolower($item['value']);
            }
            return $item;
        });

        /**
         * @see SetSettingValidator
         */
        $validator = $settings->pluck('value', 'key')->all();
        $keys = array_keys($validator);
        $vals = array_values($validator);
        if(!empty($keys[0]) && in_array($keys[0],Setting::$linkage) && !empty((int)$vals[0])){
            if(!$this->settings->get('qcloud_close','qcloud')){
                $this->outPut(ResponseCode::INVALID_PARAMETER,'请先开启云API');
            }
        }
        $this->validator->valid($validator);

        $settings->transform(function ($setting) {
            $key = Arr::get($setting, 'key');
            $value = Arr::get($setting, 'value');
            $tag = Arr::get($setting, 'tag', 'default');
            if ($key == 'site_manage' || $key == 'api_freq') {
                if (is_array($value)) {
                    $value = json_encode($value, 256);
                }
            }
            if ($key == 'password_length' && (int)$value < 6) {
                $value = "6"; // 修改数据库值
                Arr::set($setting, 'value', "6"); // 修改返回集合中的值
            }
            $this->settings->set($key, $value, $tag);
            return $setting;
        });
        $action_desc = "";
        if (!empty($settings['cash_cash_interval_time']['key'])) {
            if ($settings['cash_cash_interval_time']['key'] == 'cash_interval_time') {
                $action_desc = '更改提现设置';
            }
        }

        if (!empty($settings['wxpay_app_id']['key'])) {
            if ($settings['wxpay_app_id']['key'] == 'app_id') {
                $action_desc = '配置了微信支付';
            }
        }
        if (!empty($settings['wxpay_wxpay_close']['key'])) {
            if ($settings['wxpay_wxpay_close']['key'] == 'wxpay_close') {
                if ($settings['wxpay_wxpay_close']['value'] == 0) {
                    $action_desc = '关闭了微信支付';
                } else {
                    $action_desc = '开启了微信支付';
                }
            }
        }

        if (!empty($action_desc)) {
            AdminActionLog::createAdminActionLog(
                $user_id,
                $action_desc
            );
        }

        $this->events->dispatch(new Saved($settings));
        return $this->outPut(ResponseCode::SUCCESS, '', $settings);
    }

    /**
     * 设置分成比例
     *
     * @param int $siteAuthorScale
     * @param int $siteMasterScale
     * @param Collection $settings
     */
    private function setSiteScale(int $siteAuthorScale, int $siteMasterScale, &$settings)
    {
        $settings->put('default_site_author_scale', [
            'key' => 'site_author_scale',
            'value' => $siteAuthorScale,
            'tag' => 'default',
        ]);

        $settings->put('default_site_master_scale', [
            'key' => 'site_master_scale',
            'value' => $siteMasterScale,
            'tag' => 'default',
        ]);
    }
}
