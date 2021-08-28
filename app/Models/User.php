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

use App\Common\AuthUtils;
use App\Common\CacheKey;
use App\Common\Utils;
use App\Common\ResponseCode;
use App\Traits\Notifiable;
use Carbon\Carbon;
use Discuz\Auth\Guest;
use Discuz\Base\DzqModel;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Database\ScopeVisibilityTrait;
use Discuz\Foundation\EventGeneratorTrait;
use Discuz\Http\UrlGenerator;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $sex
 * @property string $username
 * @property string $mobile
 * @property string $password
 * @property string $pay_password
 * @property string $avatar
 * @property int $status
 * @property string $union_id
 * @property string $last_login_ip
 * @property int $last_login_port
 * @property string $register_ip
 * @property int $register_port
 * @property string $register_reason
 * @property string $reject_reason
 * @property string $signature
 * @property string $username_bout
 * @property int $thread_count
 * @property int $follow_count
 * @property int $fans_count
 * @property int $liked_count
 * @property int $question_count
 * @property Carbon $login_at
 * @property Carbon $avatar_at
 * @property Carbon $joined_at
 * @property Carbon $expired_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $identity
 * @property string $realname
 * @property int $bind_type
 * @property bool $denyStatus
 * @property Collection $groups
 * @property userFollow $follow
 * @property UserWallet $userWallet
 * @property UserWechat $wechat
 * @property UserDistribution $userDistribution
 * @property User $deny
 * @method truncate()
 * @method hasAvatar()
 */
class User extends DzqModel
{
    use EventGeneratorTrait;
    use ScopeVisibilityTrait;
    use Notifiable;

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
    ];

    /**
     * {@inheritdoc}
     */
    protected $dates = [
        'avatar_at',
        'login_at',
        'joined_at',
        'expired_at',
        'created_at',
        'updated_at',
    ];

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'id',
        'username',
        'password',
        'nickname',
        'mobile',
        'bind_type',
        'updated_at'
    ];

    const STATUS_NORMAL = 0;//正常
    const STATUS_BAN = 1;//禁用
    const STATUS_MOD = 2;//审核中
    const STATUS_REFUSE = 3;//审核不通过
    const STATUS_IGNORE = 4;//审核忽略
    const STATUS_NEED_FIELDS = 10;//待填写扩展审核字段

    /*
     * 姓名和身份证号一致
     */
    const NAME_ID_NUMBER_MATCH = 0;

    public static $statusMap = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_BAN => '禁用',
        self::STATUS_MOD => '审核中',
        self::STATUS_REFUSE => '审核不通过',
        self::STATUS_IGNORE => '审核忽略',
        self::STATUS_NEED_FIELDS => '待填写注册扩展信息'
    ];

    /**
     * 枚举 - status
     * obsolete
     * 0 正常 1 禁用 2 审核中 3 审核拒绝 4 审核忽略
     * @var array
     */
    protected static $status = [
        'normal' => 0,
        'ban' => 1,
        'mod' => 2,
        'refuse' => 3,
        'ignore' => 4,
    ];

    protected static $statusMeaning = [
        0 => '正常',
        1 => '禁用',
        2 => '审核中',
        3 => '审核拒绝',
        4 => '审核忽略',
    ];

    /**
     * An array of permissions that this user has.
     *
     * @var string[]|null
     */
    protected $permissions = null;

    /**
     * The hasher with which to hash passwords.
     *
     * @var Hasher
     */
    protected static $hasher;

    /**
     * The access gate.
     *
     * @var Gate
     */
    protected static $gate;

    /**
     * Register a new user.
     *
     * @param array data
     * @return static
     */
    public static function register(array $data)
    {
        $user = new static;
        $user->attributes = $data;
        $user->joined_at = Carbon::now();
        $user->login_at = Carbon::now();
        $user->setPasswordAttribute($user->password);

        // 将名字中的空白字符替换为空
        $user->username = preg_replace('/\s/ui', '', $user->username);
        //第三方登录绑定类型
        $user->bind_type = isset($data['bind_type']) ? $data['bind_type'] : 0;
        return $user;
    }

    /**
     * 根据 值/类型 获取对应值
     *
     * @param mixed $mixed
     * @param bool $meaning 枚举含义
     * @return mixed
     */
    public static function enumStatus($mixed, $meaning = false)
    {
        $arr = static::$status;
        $arrMeaning = static::$statusMeaning;

        if (is_numeric($mixed)) {
            if ($meaning) {
                return $arrMeaning[$mixed];
            }
            return array_search($mixed, $arr);
        }

        return $arr[$mixed];
    }

    /**
     * @return Gate
     */
    public static function getGate()
    {
        return static::$gate;
    }

    /**
     * @param Gate $gate
     */
    public static function setGate($gate)
    {
        static::$gate = $gate;
    }

    /**
     * Change the user's password.
     *
     * @param string $password
     * @return $this
     */
    public function changePassword($password)
    {
        $this->password = $password;

        // $this->raise(new PasswordChanged($this));

        return $this;
    }

    public function changePayPassword($password)
    {
        $this->pay_password = $password;

        // $this->raise(new PayPasswordChanged($this));

        return $this;
    }

    /**
     * @param string $path
     * @param bool $isRemote
     * @return $this
     */
    public function changeAvatar($path, $isRemote = false)
    {
        $this->avatar = ($isRemote ? 'cos://' : '') . $path;
        $this->avatar_at = $path ? Carbon::now() : null;

        return $this;
    }

    /**
     * @param string $path
     * @param bool $isRemote
     * @return $this
     */
    public function changeBackground($path, $isRemote = false)
    {
        $this->background = ($isRemote ? 'cos://' : '') . $path;
        return $this;
    }

    public function changeMobile($mobile)
    {
        $this->mobile = $mobile;

        $this->changeUserBindType();

        return $this;
    }

    public function changeUserBindType()
    {
        //更新用户绑定类型
        $userBindType   = empty($this->bind_type) ? 0 :$this->bind_type;
        $existBindType  = AuthUtils::getBindTypeArrByCombinationBindType($userBindType);

        if (!empty($this->mobile) && !in_array(AuthUtils::PHONE, $existBindType)) {
            //添加手机号绑定类型
            array_push($existBindType, AuthUtils::PHONE);
            $newBindType        = AuthUtils::getBindType($existBindType);
            $this->bind_type    = $newBindType;
        } elseif (empty($this->mobile) && in_array(AuthUtils::PHONE, $existBindType)) {
            //删除手机号绑定类型
            $existBindType      = array_diff($existBindType, [AuthUtils::PHONE]);
            $newBindType        = AuthUtils::getBindType($existBindType);
            $this->bind_type    = $newBindType;
        }
    }

    public function changeStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function changeRealname($realname)
    {
        $this->realname = $realname;

        return $this;
    }

    public function changeIdentity($identity)
    {
        $this->identity = $identity;

        return $this;
    }

    public function changeUpdateAt()
    {
        $this->updated_at = Carbon::now();

        return $this;
    }

    public function changeUsername($username, $isAdmin = false)
    {
        $this->username = $username;

        if (!$isAdmin) {
            // 修改次数+1
            $this->username_bout += 1;
        }

        return $this;
    }

    public function changeSignature($signature)
    {
        $this->signature = $signature;

        return $this;
    }

    public function changeNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * @return $this
     */
    public function resetGroup()
    {
        $this->groups()->sync(
            Group::query()->where('default', true)->first() ?? Group::MEMBER_ID
        );

        return $this;
    }

    /**
     * Check if a given password matches the user's password.
     *
     * @param string $password
     * @return bool
     */
    public function checkPassword($password)
    {
        return static::$hasher->check($password, $this->password);
    }

    /**
     * Check if a given password matches the user's wallet pay password.
     *
     * @param string $password
     * @return bool
     */
    public function checkWalletPayPassword($password)
    {
        return static::$hasher->check($password, $this->pay_password);
    }

    public function checkWalletPay(){
        if($this->pay_password){
            return true;
        }else{
            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 修改器
    |--------------------------------------------------------------------------
    */

    /**
     * Set the password attribute, storing it as a hash.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value ? static::$hasher->make($value) : '';
    }

    public function setPayPasswordAttribute($value)
    {
        $this->attributes['pay_password'] = $value ? static::$hasher->make($value) : '';
    }

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    public function getAvatarAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }
        if (strpos($value, '://') === false) {
            return app(UrlGenerator::class)->to('/storage/avatars/' . $value)
                . '?' . Carbon::parse($this->avatar_at)->timestamp;
        }

        /** @var SettingsRepository $settings */
        $settings = app(SettingsRepository::class);

        $path = 'public/avatar/' . Str::after($value, '://');

        if ($settings->get('qcloud_cos_sign_url', 'qcloud', true)) {
            return app(Filesystem::class)->disk('avatar_cos')->temporaryUrl($path, Carbon::now()->addDay());
        } else {
            return app(Filesystem::class)->disk('avatar_cos')->url($path)
                . '?' . Carbon::parse($this->avatar_at)->timestamp;
        }
    }

    public function getOriginalAvatarPath()
    {
        $uid = sprintf('%09d', $this->id);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        $originalAvatar = $dir1.'/'.$dir2.'/'.$dir3.'/original_'.substr($uid, -2).'.png';

        $value = $this->getRawOriginal('avatar');
        if (empty($value)) {
            return $value;
        }
        if (strpos($value, '://') === false) {
            return app(UrlGenerator::class)->to('/storage/avatars/' . $originalAvatar)
                . '?' . Carbon::parse($this->avatar_at)->timestamp;
        }

        /** @var SettingsRepository $settings */
        $settings = app(SettingsRepository::class);

        $path = 'public/avatar/' . $originalAvatar;

        if ($settings->get('qcloud_cos_sign_url', 'qcloud', true)) {
            return app(Filesystem::class)->disk('avatar_cos')->temporaryUrl($path, Carbon::now()->addDay());
        } else {
            return app(Filesystem::class)->disk('avatar_cos')->url($path)
                . '?' . Carbon::parse($this->avatar_at)->timestamp;
        }
    }

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    public function getBackgroundAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        if (strpos($value, '://') === false) {
            return app(UrlGenerator::class)->to('/storage/background/' . $value);
        }

        /** @var SettingsRepository $settings */
        $settings = app(SettingsRepository::class);

        $path = 'public/background/' . Str::after($value, '://');

        if ($settings->get('qcloud_cos_sign_url', 'qcloud', true)) {
            return app(Filesystem::class)->disk('background_cos')->temporaryUrl($path, Carbon::now()->addDay());
        } else {
            return app(Filesystem::class)->disk('background_cos')->url($path);
        }
    }

    public function getOriginalBackGroundPath()
    {
        $uid = sprintf('%09d', $this->id);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);

        $value = $this->getRawOriginal('background');
        if (empty($value)) {
            return $value;
        }
        $backUrl = str_replace($dir1.'/'.$dir2.'/'.$dir3.'/',$dir1.'/'.$dir2.'/'.$dir3.'/'.'original_',$value);
        if (strpos($value, '://') === false) {
            return app(UrlGenerator::class)->to('/storage/background/' . $backUrl);
        }
        $originalBackground = str_replace('cos://','',$backUrl);

        /** @var SettingsRepository $settings */
        $settings = app(SettingsRepository::class);

        $path = 'public/background/' . $originalBackground;

        if ($settings->get('qcloud_cos_sign_url', 'qcloud', true)) {
            return app(Filesystem::class)->disk('background_cos')->temporaryUrl($path, Carbon::now()->addDay());
        } else {
            return app(Filesystem::class)->disk('background_cos')->url($path);
        }
    }

    public function getMobileAttribute($value)
    {
        return $value ? substr_replace($value, '****', 3, 4) : '';
    }

    public function getRealnameAttribute($value)
    {
        return $value ?: '';
    }

    public function getIdentityAttribute($value)
    {
        return $value ? substr_replace($value, '****************', 1, 16) : '';
    }

    /*
    |--------------------------------------------------------------------------
    | 常用方法
    |--------------------------------------------------------------------------
    */

    /**
     * Refresh the thread's comments count.
     *
     * @return $this
     */
    public function refreshThreadCount()
    {
        $this->thread_count = $this->threads()
            ->where('is_approved', Thread::APPROVED)
            ->where('is_draft', Thread::IS_NOT_DRAFT)
            ->where('is_display', Thread::BOOL_YES)
            ->whereNull('deleted_at')
            ->count();

        return $this;
    }

    /**
     * 刷新用户问答数，包括提问与回答
     *
     * @return $this
     */
    public function refreshQuestionCount()
    {
        $userId = $this->id;
        $query = Thread::query();
        $query->where('threads.type', Thread::TYPE_OF_QUESTION);
        $query->where('threads.is_approved', Thread::APPROVED);
        $query->where('threads.is_draft', '<>', 1);
        $query->whereNull('threads.deleted_at');
        $query->leftJoin('questions', 'threads.id', '=', 'questions.thread_id');
        $query->where(function (Builder $query) use ($userId) {
            $query->where('threads.user_id', $userId)->orWhere('questions.be_user_id', $userId);
        });
        $this->question_count = $query->count();

        return $this;
    }

    public function getUnreadNotificationCount()
    {
        static $cached = null;
        if (is_null($cached)) {
            $cached = $this->unreadNotifications()->count();
        }
        return $cached;
    }

    public function getUnreadTypesNotificationCount()
    {
        static $cachedAll = null;
        if (is_null($cachedAll)) {
            $cachedAll = $this->unreadNotifications()
                ->selectRaw('type,count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type');
        }
        return $cachedAll;
    }

    /**
     * Check whether or not the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin()
    {
        if ($this instanceof Guest) {
            return false;
        }

        return $this->groups->contains(Group::ADMINISTRATOR_ID);
    }

    /**
     * Check whether or not the user is a guest.
     *
     * @return bool
     */
    public function isGuest()
    {
        return false;
    }

    /**
     * 刷新用户关注数
     * @return $this
     */
    public function refreshUserFollow()
    {
        $this->follow_count = UserFollow::query()->where('from_user_id',$this->id)->groupBy("to_user_id")->get("to_user_id")->count();
        return $this;
    }

    /**
     * 刷新用户粉丝数
     * @return $this
     */
    public function refreshUserFans()
    {
        $this->fans_count = UserFollow::query()->where('to_user_id',$this->id)->groupBy("from_user_id")->get("from_user_id")->count();
        return $this;
    }

    /**
     * 刷新用户点赞主题数
     * @return $this
     */
    public function refreshUserLiked()
    {
        $this->liked_count = $this->postUser()
            ->join('posts', 'post_user.post_id', '=', 'posts.id')
            ->join('threads','posts.thread_id','=','threads.id')
            ->where('posts.is_first', true)
            ->where('posts.is_approved', Post::APPROVED)
            ->whereNull('posts.deleted_at')
            ->whereNotNull('threads.user_id')
            ->where('threads.is_display',Thread::BOOL_YES)
            ->where('threads.is_sticky', Thread::BOOL_NO)
            ->where('threads.is_draft', Thread::IS_NOT_DRAFT)
            ->count();

        return $this;
    }

    /**
     * 注册用户创建一个随机用户名
     *
     * @return string
     */
    public static function getNewUsername()
    {
        $username = trans('validation.attributes.username_prefix') . Str::random(6);
        $user = User::query()->where('username', $username)->first();
        if ($user) {
            return self::getNewUsername();
        }
        return $username;
    }

    /**
     * 给用户名拼接随机字符串
     *
     * @return string
     */
    public static function addStringToUsername($content = '')
    {
        $preName = !empty($content) ? $content : trans('validation.attributes.username_prefix');
        $username = $preName . Str::random(6);
        $user = User::query()->where('username', $username)->first();
        if ($user) {
            return self::addStringToUsername($content);
        }
        return $username;
    }

    /**
     * 给昵称拼接随机字符串
     *
     * @return string
     */
    public static function addStringToNickname($content = '')
    {
        $preName = !empty($content) ? $content : trans('validation.attributes.username_prefix');
        $nickname = $preName . Str::random(6);
        $user = User::query()->where('nickname', $nickname)->first();
        if ($user) {
            return self::addStringToNickname($content);
        }
        return $nickname;
    }

    /**
     * 判断是否有上级 & 上级是否可以推广下线分成
     *
     * @param int $type 1推广下线 2/3收入提成
     * @return bool
     */
    public function isAllowScale($type)
    {
        switch ($type) {
            case Order::ORDER_TYPE_REGISTER:
                // 注册分成查询付款人的上级
                if (!empty($userDistribution = $this->userDistribution)) {
                    return (bool)$userDistribution->is_subordinate;
                }
                break;
            case Order::ORDER_TYPE_REWARD:
            case Order::ORDER_TYPE_THREAD:
                // 打赏/付费分成查询收款人的上级
                if (!empty($userDistribution = $this->userDistribution)) {
                    return (bool)$userDistribution->is_commission;
                }
                break;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | 关联模型
    |--------------------------------------------------------------------------
    */

    public function logs()
    {
        return $this->morphMany(UserActionLogs::class, 'log_able');
    }

    public function latelyLog()
    {
        return $this->hasOne(UserActionLogs::class, 'log_able_id')->orderBy('id', 'desc');
    }

    public function wechat()
    {
        return $this->hasOne(UserWechat::class);
    }

    /**
     * Define the relationship with the user's posts.
     *
     * @return HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Define the relationship with the user's threads.
     *
     * @return HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Define the relationship with the user's orders.
     *
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Define the relationship with the user's groups.
     *
     * @return BelongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class)
            ->withPivot('expiration_time');
    }

    public function extFields()
    {
        return $this->hasMany(UserSignInFields::class, 'user_id');
    }

    /**
     * Define the relationship with the user's favorite threads.
     *
     * @return BelongsToMany
     */
    public function favoriteThreads()
    {
        return $this->belongsToMany(Thread::class)
            ->as('favoriteState')
            ->withPivot('created_at')
            ->whereNull('threads.deleted_at')
            ->whereNotNull('threads.user_id')
            ->where('threads.is_approved', Thread::APPROVED);
    }

    /**
     * Define the relationship with the user's liked posts.
     *
     * @return BelongsToMany
     */
    public function likedPosts()
    {
        return $this->belongsToMany(Post::class);
    }

    /**
     * Define the relationship with the user's wallet.
     *
     * @return hasOne
     */
    public function userWallet()
    {
        return $this->hasOne(UserWallet::class);
    }

    /**
     * Define the relationship with the user's follow.
     *
     * @return HasMany
     */
    public function userFollow()
    {
        return $this->hasMany(UserFollow::class, 'from_user_id');
    }

    /**
     * Define the relationship with the user's fans.
     *
     * @return HasMany
     */
    public function userFans()
    {
        return $this->hasMany(UserFollow::class, 'to_user_id');
    }

    public function postUser()
    {
        return $this->hasMany(PostUser::class);
    }

    public function userDistribution()
    {
        return $this->hasOne(UserDistribution::class);
    }

    /*
    |--------------------------------------------------------------------------
    | 权限验证
    |--------------------------------------------------------------------------
    */

    /**
     * Define the relationship with the permissions of all of the groups that
     * the user is in.
     *
     * @return Builder
     */
    public function permissions()
    {
        $groupIds = (Arr::get($this->getRelations(), 'groups') ?? $this->groups)->pluck('id')->all();

        return Permission::query()->whereIn('group_id', $groupIds);
    }

    /**
     * Get a list of permissions that the user has.
     *
     * @return string[]
     */
    public function getPermissions()
    {
        return $this->permissions()->pluck('permission')->all();
    }

    /**
     * 检查用户是否具有一定的权限基于他们的用户组。
     * 传入字符串时，返回是否具有此权限。
     * 传入数组时，如果第二个参数为 true (default) 返回是否同时具有这些权限，
     * 如果第二个参数为 false 则返回是否具有这些权限其中之一。
     *
     * @param string|array $permission
     * @param bool $condition
     * @return bool
     */
    public function hasPermission($permission, bool $condition = true)
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (is_null($this->permissions)) {
            $this->permissions = Permission::getUserPermissions($this);
        }

        if (is_array($permission)) {
            foreach ($permission as $item) {
                if ($condition) {
                    if (!in_array($item, $this->permissions)) {
                        return false;
                    }
                } else {
                    if (in_array($item, $this->permissions)) {
                        return true;
                    }
                }
            }

            return $condition;
        } else {
            return in_array($permission, $this->permissions);
        }
    }

    /**
     * @param string $ability
     * @param array|mixed $arguments
     * @return bool
     */
    public function can($ability, $arguments = [])
    {
        return static::$gate->forUser($this)->allows($ability, $arguments);
    }

    /**
     * @param string $ability
     * @param array|mixed $arguments
     * @return bool
     */
    public function cannot($ability, $arguments = [])
    {
        return !$this->can($ability, $arguments);
    }

    /**
     * Set the hasher with which to hash passwords.
     *
     * @param Hasher $hasher
     */
    public static function setHasher(Hasher $hasher)
    {
        static::$hasher = $hasher;
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeHasAvatar($query)
    {
        return $query->whereNotNull('avatar');
    }

    /**
     * @return mixed
     */
    public function deny()
    {
        return $this->belongsToMany(User::class, 'deny_users', 'user_id', 'deny_user_id', null, null, 'deny');
    }

    /**
     * @return mixed
     */
    public function denyFrom()
    {
        return $this->belongsToMany(User::class, 'deny_users', 'deny_user_id', 'user_id', null, null, 'denyFrom');
    }

    protected function insertAndSetId(Builder $query, $attributes)
    {
        //查询是否需要填写扩展字段
        $settings = app(SettingsRepository::class);
        $open_ext_fields = $settings->get('open_ext_fields');
        if ($open_ext_fields) {
            $attributes['status'] = User::STATUS_NEED_FIELDS;
        }
        if (isset($attributes['register_port']) && empty($attributes['register_port'])) {
            $attributes['register_port'] = 0;
        }
        if (isset($attributes['last_login_port']) && empty($attributes['last_login_port'])) {
            $attributes['last_login_port'] = 0;
        }
        parent::insertAndSetId($query, $attributes); // TODO: Change the autogenerated stub
        $cache = app('cache');
        $cacheKey = CacheKey::NEW_USER_LOGIN . $this->id;
        $d = [
            'userId' => $this->id,
            'timestamp' => time()
        ];
        $cache->put($cacheKey, json_encode($d), 3600);
    }

    //修改user的status为2，待审核状态
    public static function setUserStatusMod($userId)
    {
        $user = User::query()->find($userId);
        if (!empty($user)) {
            $user->status = self::STATUS_MOD;
        }
        return $user->save();
    }

    public static function setUserStatusNormal($userId)
    {
        $user = User::query()->find($userId);
        if (!empty($user)) {
            $user->status = self::STATUS_NORMAL;
        }
        return $user->save();
    }

    public static function isStatusMod($userId)
    {
        $user = User::query()->find($userId);
        if (!empty($user)) {
            if ($user->status == self::STATUS_MOD) {
                return true;
            }
        }
        return false;
    }

    public static function getUserReject($userId)
    {
        $user = User::query()->find($userId);
        if (empty($user)) {
            return false;
        }
        return [
            'id' => $user['id'],
            'userName' => $user['username'],
            'rejectReason' => $user['reject_reason']
        ];
    }

    public function getUsers($userIds)
    {
        return self::query()->whereIn('id', $userIds)->get()->toArray();
    }

    public function getUserName($userId)
    {
        $user = self::query()->find($userId);
        if (empty($user)) {
            return null;
        }
        return $user->username;
    }

    protected function clearCache()
    {
        \Discuz\Base\DzqCache::delHashKey(CacheKey::LIST_THREADS_V3_USERS, $this->id);
    }
}
