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

namespace App\Commands\Users;

use App\Censor\Censor;
use App\Censor\CensorNotPassedException;
use App\Common\ResponseCode;
use App\Events\Users\Registered;
use App\Events\Users\Saving;
use App\Models\User;
use Carbon\Carbon;
use Discuz\Base\DzqLog;
use Discuz\Common\Utils;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Foundation\EventsDispatchTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;

class AutoRegisterUser
{
    use EventsDispatchTrait;

    public $actor;

    public $data;

    /**
     * @param User $actor The user performing the action.
     * @param array $data The attributes of the new user.
     */
    public function __construct(User $actor, array $data)
    {
        $this->actor = $actor;
        $this->data = $data;
    }

    /**
     * @param Dispatcher $events
     * @param Censor $censor
     * @param SettingsRepository $settings
     * @return User
     */
    public function handle(Dispatcher $events, Censor $censor, SettingsRepository $settings)
    {
        $this->events = $events;
        $request = app('request');

        $this->data['register_ip'] = ip($request->getServerParams());
        $this->data['register_port'] = Arr::get($request->getServerParams(), 'REMOTE_PORT', 0);
        //自动注册没有密码，后续用户可以设置密码
        $this->data['password'] = '';

        DzqLog::info('begin_auto_register_user_process_checkText', ['data' => $this->data] ,DzqLog::LOG_LOGIN);
        // 敏感词校验
        try {
            $username = $censor->checkText(Arr::get($this->data, 'username'), 'username');
        } catch (\Exception $e) {
            DzqLog::error('checkText_error', [
                'username'   => Arr::get($this->data, 'username')
            ], $e->getMessage());
            Utils::outPut(ResponseCode::NET_ERROR, '敏感词检测异常');
        }

        DzqLog::info('end_auto_register_user_process_checkText', ['data' => $this->data] ,DzqLog::LOG_LOGIN);
        $username = preg_replace('/\s/ui', '', $username);
        $exists = User::where('username', $username)->exists();
        if ($exists) {
            $this->data['username'] = User::getNewUsername();
        } else {
            $this->data['username'] = $username;
        }
        DzqLog::info('auto_register_user_process', ['data' => $this->data] ,DzqLog::LOG_LOGIN);
        $this->data['nickname'] = $censor->checkText(Arr::get($this->data, 'nickname'), 'nickname');

        // 审核模式，设置注册为审核状态
        if ($settings->get('register_validate')) {
            $this->data['register_reason'] = $this->data['register_reason'] ?: trans('user.register_by_auto');
            $this->data['status'] = 2;
        }

        // 付费模式，默认注册时即到期
        if ($settings->get('site_mode') == 'pay') {
            $this->data['expired_at'] = Carbon::now();
        }

        //扩展字段
        if ($settings->get('open_ext_fields')) {
            $this->data['status'] = User::STATUS_NEED_FIELDS;
        }

        $this->data['bind_type'] = !empty($this->data['bind_type']) ? $this->data['bind_type'] : 0;
        $user = User::register(Arr::only($this->data, [
            'username', 'nickname', 'password', 'bind_type',
            'register_ip', 'register_port', 'register_reason',
            'status'
        ]));

        $this->events->dispatch(
            new Saving($user, $this->actor, $this->data)
        );

        $user->save();

        $user->raise(new Registered($user, $this->actor, $this->data));

        $this->dispatchEventsFor($user, $this->actor);

        return $user;
    }
}
