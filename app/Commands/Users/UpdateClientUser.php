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
use App\Events\Users\PayPasswordChanged;
use App\Exceptions\TranslatorException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Validators\UserValidator;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Foundation\EventsDispatchTrait;
use Discuz\SpecialChar\SpecialCharServer;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\UsernameChange;

class UpdateClientUser
{
    use AssertPermissionTrait;
    use EventsDispatchTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Collection|array
     */
    protected $data;

    /**
     * @var User
     */
    protected $actor;

    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var UserValidator
     */
    protected $validator;

    /**
     * @var SettingsRepository
     */
    protected $settings;

    /**
     * @var Censor
     */
    protected $censor;

    /**
     * @var SpecialCharServer
     */
    protected $specialChar;

    /**
     * @param int $id
     * @param Collection|array $data
     * @param User $actor
     */
    public function __construct($id, $data, User $actor)
    {
        $this->id = $id;
        $this->data = $data;
        $this->actor = $actor;
    }

    /**
     * @param UserRepository $users
     * @param UserValidator $validator
     * @param Dispatcher $events
     * @param SettingsRepository $settings
     * @param Censor $censor
     * @param SpecialCharServer $specialChar
     * @return mixed
     */
    public function handle(UserRepository $users, UserValidator $validator, Dispatcher $events, SettingsRepository $settings, Censor $censor, SpecialCharServer $specialChar)
    {
        $this->users = $users;
        $this->validator = $validator;
        $this->events = $events;
        $this->settings = $settings;
        $this->censor = $censor;
        $this->specialChar = $specialChar;

        return call_user_func([$this, '__invoke']);
    }

    /**
     * @return User
     * @throws CensorNotPassedException
     * @throws GuzzleException
     * @throws InvalidConfigException
     * @throws PermissionDeniedException
     * @throws TranslatorException
     * @throws ValidationException
     */
    public function __invoke()
    {
        $user = $this->users->findOrFail($this->id, $this->actor);
        $canEdit = true;
        $isSelf = $this->actor->id === $user->id;
        if(!$isSelf){
            throw new PermissionDeniedException('没有权限');
        }
        if(!empty(Arr::get($this->data, 'data.attributes'))){
            $attributes = Arr::get($this->data, 'data.attributes');
        }else{
            $attributes = $this->data;
        }
        // 下列部分方法使用地址引用的方式修改了该值，以便验证用户参数
        $validate = [];

        // 修改用户名
        $this->rename($user, $canEdit, $isSelf, $attributes, $validate);

        // 修改昵称
        $this->changeNickname($user, $canEdit, $isSelf, $attributes, $validate);

        // 修改登录密码
        $this->changePassword($user, $canEdit, $isSelf, $attributes, $validate);

        // 修改支付密码
        $this->changePayPassword($user, $isSelf, $attributes, $validate);

        // 修改用户签名
        $this->changeSignature($user, $canEdit, $isSelf, $attributes);
        //$this->validator->valid($validate);
        $user->save();

        $this->dispatchEventsFor($user, $this->actor);

        return $user;
    }

    /**
     * @param User $user
     * @param bool $canEdit
     * @param bool $isSelf
     * @param array $attributes
     * @param array $validate
     * @return array
     * @throws CensorNotPassedException
     * @throws GuzzleException
     * @throws InvalidConfigException
     * @throws PermissionDeniedException
     * @throws TranslatorException
     */
    protected function rename(User $user, bool $canEdit, bool $isSelf, array $attributes, array &$validate)
    {
        $username = Arr::get($attributes, 'username');

        if (! $username || $username == $user->username) {
            return $validate;
        }

        $old_username = $user->username;

        // 敏感词校验
        $this->censor->checkText($username, 'username');

        if ($this->censor->isMod) {
            throw new TranslatorException(trans('user.user_username_censor_error'));
        }

        // 过滤内容
        $username = $this->specialChar->purify($username);

        $usernamechange = UsernameChange::query()->where("user_id",$user->id)->orderBy('id', 'desc')
            ->first();
        if($usernamechange){
            $currentTime=date("y-m-d h:i:s");
            $oldTime=$usernamechange->updated_at;
            if(strtotime($currentTime)<strtotime("+1years",strtotime($oldTime))){
                throw new TranslatorException(trans('user.user_username_change_limit_error'));
            }
        }
        $user->changeUsername($username);
        //更改用户名记录
        $usernameChange = new UsernameChange();
        $usernameChange->user_id = $user->id;
        $usernameChange->number = 1;
        $usernameChange->save();


        $user->changeUsername($username);
        $validate['username'] = $username;

        return $validate;
    }

    protected function changeNickname(User $user, bool $canEdit, bool $isSelf, array $attributes, array &$validate)
    {
        $nickname = Arr::get($attributes, 'nickname');

        if (! $nickname || $nickname == $user->nickname) {
            return $validate;
        }

        // 过滤内容
        $nickname = $this->specialChar->purify($nickname);
        $user->changeNickname($nickname);
        $validate['nickname'] = $nickname;

        return $validate;
    }



    /**
     * @param User $user
     * @param bool $canEdit
     * @param bool $isSelf
     * @param array $attributes
     * @param array $validate
     * @return array
     * @throws PermissionDeniedException
     * @throws TranslatorException
     */
    protected function changePassword(User $user, bool $canEdit, bool $isSelf, array $attributes, array &$validate)
    {
        $newPassword = Arr::get($attributes, 'newPassword');
        if (! $newPassword) {
            return $validate;
        }

        if ($isSelf) {
            $user->changePassword($newPassword);
        }
        $validate['password'] = $newPassword;
        return $validate;
    }

    /**
     * @param User $user
     * @param bool $isSelf
     * @param array $attributes
     * @param array $validate
     * @return array
     * @throws TranslatorException
     */
    protected function changePayPassword(User $user, bool $isSelf, array $attributes, array &$validate)
    {
        $payPassword = Arr::get($attributes, 'payPassword');

        if (! $payPassword) {
            return $validate;
        }

        // 当原支付密码为空时，视为初始化支付密码，不需要验证 pay_password_token
        // 当原支付密码不为空时，则需验证 pay_password_token

        $user->changePayPassword($payPassword);

        $validate['pay_password'] = $payPassword;
        $validate['pay_password_confirmation'] = Arr::get($attributes, 'pay_password_confirmation');

        // 修改支付密码事件
        $user->raise(new PayPasswordChanged($user));

        return $validate;
    }

    /**
     * @param User $user
     * @param bool $canEdit
     * @param bool $isSelf
     * @param array $attributes
     * @throws CensorNotPassedException
     * @throws GuzzleException
     * @throws InvalidConfigException
     * @throws PermissionDeniedException
     * @throws TranslatorException
     */
    protected function changeSignature(User $user, bool $canEdit, bool $isSelf, array $attributes)
    {
        if (! Arr::has($attributes, 'signature')) {
            return;
        }

        if ($signature = Arr::get($attributes, 'signature', '')) {
            // 敏感词校验
            $this->censor->checkText($signature, 'signature');

            // 过滤内容
            $signature = $this->specialChar->purify($signature);
        }
        if (Str::of($signature)->length() > 140) {
            throw new TranslatorException(trans('user.user_signature_limit_error'));
        }

        $user->changeSignature($signature);
    }
}
