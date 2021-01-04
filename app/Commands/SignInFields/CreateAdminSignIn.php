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

namespace App\Commands\SignInFields;


use App\Models\AdminSignInFields;
use App\Models\User;


class CreateAdminSignIn
{
//    use AssertPermissionTrait;
//    use EventsDispatchTrait;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes of the new thread.
     *
     * @var array
     */
    public $data;

    /**
     * The current ip address of the actor.
     *
     * @var array
     */
    public $ip;

    /**
     * The current port of the actor.
     *
     * @var int
     */
    public $port;

    /**
     * @param User $actor
     * @param array $data
     * @param string $ip
     * @param string $port
     */
    public function __construct(User $actor, array $data, $ip, $port)
    {
        $this->actor = $actor;
        $this->data = $data;
        $this->ip = $ip;
        $this->port = $port;
    }

    /**
     *vendor/illuminate/bus/Dispatcher.php
     */
    public function handle()
    {
        if (isset($this->data['attributes'])) {
            $attributes = [$this->data['attributes']];
        } else {
            $attributes = array_column($this->data, 'attributes');
        }
        $data = [];
        foreach ($attributes as $attribute) {
            if (!empty($attribute['id'])) {
                $adminSignIn = AdminSignInFields::query()->where('id', $attribute['id'])->first();
                if (empty($adminSignIn)) {
                    continue;
                }
            }else{
                $adminSignIn = new AdminSignInFields();
            }
            foreach ($attribute as $key => $value) {
                in_array($key, ['name', 'type', 'fields_ext', 'fields_desc', 'sort', 'status','required']) && $adminSignIn[$key] = $value;
            }
            $adminSignIn->save() && $data[] = $adminSignIn;
        }
        return $data;
    }
}
