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

use App\Models\DenyUser;
use App\Models\User;
use App\Models\UsernameChange;
use App\Models\UserQq;
use App\Repositories\UserFollowRepository;
use Discuz\Api\Serializer\AbstractSerializer;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tobscure\JsonApi\Relationship;
use Psr\Http\Message\ServerRequestInterface;

class UserV2Serializer extends AbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'users';

    /**
     * @var Gate
     */
    protected $gate;

    protected $userFollow;

    protected $settings;

    protected $request;

    /**
     * @param Gate $gate
     * @param UserFollowRepository $userFollow
     */
    public function __construct(Gate $gate, UserFollowRepository $userFollow,SettingsRepository $settings,ServerRequestInterface $request)
    {
        $this->gate = $gate;
        $this->userFollow = $userFollow;
        $this->settings = $settings;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     *
     * @param User $model
     */
    public function getDefaultAttributes($model)
    {
        $gate = $this->gate->forUser($this->actor);

        $canEdit = $gate->allows('edit', $model);

        $attributes = [
            'id'                => (int) $model->id,
            'username'          => $model->username,
            'avatarUrl'         => $model->avatar,
            'originalAvatarUrl' => $this->getOriginalAvatar($model),
            'backgroundUrl'     => $model->background,
            'originalBackGroundUrl'     => $this->getOriginalBackGround($model),
            'isReal'            => $this->getIsReal($model),
            'threadCount'       => (int) $model->thread_count,
            'followCount'       => (int) $model->follow_count,
            'fansCount'         => (int) $model->fans_count,
            'likedCount'        => (int) $model->liked_count,
            'questionCount'     => (int) $model->question_count,
            'signature'         => $model->signature,
            'usernameBout'      => (int) $model->username_bout,
            'status'            => $model->status,
            'loginAt'           => optional($model->login_at)->format('Y-m-d H:i:s'),
            //'joinedAt'          => optional($model->joined_at)->format('Y-m-d H:i:s'),
            'expiredAt'         => optional($model->expired_at)->format('Y-m-d H:i:s'),
            //'createdAt'         => optional($model->created_at)->format('Y-m-d H:i:s'),
            'updatedAt'         => optional($model->updated_at)->format('Y-m-d H:i:s'),
            'canEdit'           => $canEdit,
            'canDelete'         => $gate->allows('delete', $model),
            'showGroups'        => $gate->allows('showGroups', $model),     // 是否显示用户组
            'registerReason'    => $model->register_reason,                 // 注册原因
            'banReason'         => '',                                      // 禁用原因
            'denyStatus'        => (bool) $model->denyStatus,
            'canBeAsked'        => $model->id !== $this->actor->id && $model->can('canBeAsked'), // 是否允许被提问
            'hasPassword'       => !empty($model->password) ? true : false,
            'nickname'         => $model->nickname,
        ];
        $whitelist = [
            '/api/follow/',
            '/api/users/'.Arr::get($this->getRequest()->getQueryParams(), 'id', '').'/',
            '/api/threads/'.Arr::get($this->getRequest()->getQueryParams(), 'id', '').'/'
        ];
        if (Str::contains($this->getRequest()->getUri()->getPath().'/', $whitelist)) {
            //需要时再查询关注状态 存在n+1
            $attributes['follow'] = $this->userFollow->findFollowDetail($this->actor->id, $model->id);
        }else{
            // 用户详情用到，所以增加这个字段
            $attributes['follow'] = $this->userFollow->findFollowDetail($this->actor->id, $model->id);
        }

        // 判断禁用原因
        if ($model->status == 1) {
            $attributes['banReason'] = !empty($model->latelyLog) ? $model->latelyLog->message : '' ;
        }


        // 限制字段 本人/权限 显示
        if ($canEdit || $this->actor->id === $model->id) {
            $attributes += [
                'originalMobile'    => $model->getRawOriginal('mobile'),
//                'registerIp'        => $model->register_ip,
//                'registerPort'      => $model->register_port,
                //'lastLoginIp'       => $model->last_login_ip,
                //'lastLoginPort'     => $model->last_login_port,
                'identity'          => $model->identity,
                'realname'          => $model->realname,
                'mobile'            => $model->mobile,
                'hasPassword'       => $model->password ? true : false,
            ];
        }

        // 钱包余额
        if ($this->actor->id === $model->id) {
            $attributes += [
                'canWalletPay'  => $this->actor->checkWalletPay(),
                'walletBalance' => $this->actor->userWallet->available_amount,
                'walletFreeze'  => $this->actor->userWallet->freeze_amount,
            ];
        }

        if($model->bind_type == 2) {
            $attributes['avatarUrl'] = ! empty($attributes['avatarUrl']) ? $attributes['avatarUrl'] : $this->qqAvatar($model);
        }

        $attributes += [
            'paid' => $model->paid,
            'payTime' => $this->formatDate($model->payTime),
//            'unreadNotifications' => $model->getUnreadNotificationCount(),
//            'typeUnreadNotifications' => $model->getUnreadTypesNotificationCount()
        ];
        //是否屏蔽
        if($this->actor->id != $model->id){
           $denyUser = DenyUser::query()
                ->where('user_id',$this->actor->id)
                ->where('deny_user_id',$model->id)
                ->first();
           $isDeny = false;
           if($denyUser){
               $isDeny = true;
           }
           $attributes += [
              'isDeny' => $isDeny
           ];
        }
        if ($this->actor->id === $model->id) {
            //是否可以修改用户名
            $canEditUsername = true;
            $usernamechange = UsernameChange::query()->where("user_id",$model->id)->orderBy('id', 'desc')
                ->first();
            if($usernamechange){
                $currentTime=date("y-m-d h:i:s");
                $oldTime=$usernamechange->updated_at;
                if(strtotime($currentTime)<strtotime("+1years",strtotime($oldTime))){
                    $canEditUsername = false;
                }
            }
            $attributes += [
                'canEditUsername' => $canEditUsername
            ];
        }

        $serverParams = $this->request->getServerParams();
        if (strpos($serverParams['REQUEST_URI'], 'backAdmin') !== false) {
            $attributes += [
                'createdAt' => optional($model->created_at)->format('Y-m-d H:i:s'),
                'registerIp'        => $model->register_ip,
                'lastLoginIp'       => $model->last_login_ip
            ];
        }

        return $attributes;
    }

    public function getOriginalAvatar($model){
        $uid = sprintf('%09d', $model->id);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $originalAvatar = $dir1.'/'.$dir2.'/'.$dir3.'/original_'.substr($uid, -2).'.png';
        $avatar = $model->getRawOriginal('avatar');
        $originalAvatarUrl = $model->getOriginalAvatarPath();
        if (strpos($avatar, '://') === false) {
            if(!file_exists(storage_path('app/public/avatars/' . $originalAvatar))){
                $originalAvatarUrl = $model->avatar;
            }
        }else{
            $fileData = @file_get_contents($originalAvatarUrl,false, stream_context_create(['ssl'=>['verify_peer'=>false, 'verify_peer_name'=>false]]));
            if(!$fileData){
                $originalAvatarUrl = $model->avatar;
            }
        }
        return $originalAvatarUrl;
    }

    public function getOriginalBackGround($model){
        $uid = sprintf('%09d', $model->id);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);

        $background = $model->getRawOriginal('background');
        $originalBackGroundUrl = $model->getOriginalBackGroundPath();
        if (strpos($background, '://') === false) {
            $backUrl = str_replace($dir1.'/'.$dir2.'/'.$dir3.'/',$dir1.'/'.$dir2.'/'.$dir3.'/'.'original_',$background);
            if(!file_exists(storage_path('app/public/background/' . $backUrl))){
                $originalBackGroundUrl = $model->background;
            }
        }else{
            $fileData = @file_get_contents($originalBackGroundUrl,false, stream_context_create(['ssl'=>['verify_peer'=>false, 'verify_peer_name'=>false]]));
            if(!$fileData){
                $originalBackGroundUrl = $model->background;
            }
        }
        return $originalBackGroundUrl;
    }

    /**
     * 是否实名认证
     *
     * @param User $model
     * @return string
     */
    public function getIsReal(User $model)
    {
        if (isset($model->realname) && $model->realname != null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $user
     * @return Relationship
     */
    public function wechat($user)
    {
        return $this->hasOne($user, UserWechatSerializer::class);
    }


    /**
     * @param $user
     * @return Relationship
     */
    public function groups($user)
    {
        return $this->hasMany($user, GroupSerializer::class);
    }

    public function extFields($user)
    {
        return $this->hasMany($user, UserSignInSerializer::class);
    }
    /**
     * @param $user
     * @return Relationship
     */
    public function deny($user)
    {
        return $this->hasMany($user, UserSerializer::class);
    }

    /**
     * @param $user
     * @return Relationship
     */
    public function dialog($user)
    {
        return $this->hasOne($user, DialogSerializer::class);
    }

    /**
     * qq头像
     * @param User $user
     * @return string
     */
    public function qqAvatar(User $user)
    {
        $qqUser = UserQq::where('user_id', $user->id)->first();
        if(! $qqUser) {
            return '';
        }
        return $qqUser->headimgurl;
    }
}
