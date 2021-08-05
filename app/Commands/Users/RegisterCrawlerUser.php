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
use App\Events\Users\Registered;
use App\Events\Users\Saving;
use App\Exceptions\TranslatorException;
use App\Models\User;
use App\Validators\UserValidator;
use Carbon\Carbon;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Foundation\EventsDispatchTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class RegisterCrawlerUser
{
    use EventsDispatchTrait;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes of the new user.
     *
     * @var array
     */
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
     * @param UserValidator $validator
     * @return User
     * @throws ValidationException
     * @throws TranslatorException
     */
    public function handle(Dispatcher $events, Censor $censor, SettingsRepository $settings, UserValidator $validator)
    {
        $this->events = $events;

        $password = Arr::get($this->data, 'password');
        $password_confirmation = Arr::get($this->data, 'password_confirmation');

        // 敏感词校验
        //用户名校验-暂不需要
        //$censor->checkText(Arr::get($this->data, 'username'), 'username');
        //昵称校验-暂不需要
        //$censor->checkText(Arr::get($this->data, 'nickname'), 'nickname');

        if (!empty($password)) {
            $this->data['register_reason'] = trans('user.register_by_username');
        }
        $user = User::register(Arr::only($this->data, ['username', 'password', 'nickname','register_ip', 'register_port', 'register_reason']));

        // 付费模式，默认注册时即到期
        if ($settings->get('site_mode') == 'pay') {
            $user->expired_at = Carbon::now()->addYears(5)->timestamp;
        }

        //todo 暂时未使用到
        $this->events->dispatch(
            new Saving($user, $this->actor, $this->data)
        );

        $user->save();
        $user->raise(new Registered($user, $this->actor, $this->data));

        $this->dispatchEventsFor($user, $this->actor);

        return $user;
    }
}
