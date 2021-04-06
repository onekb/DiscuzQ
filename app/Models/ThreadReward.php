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

use App\Events\ThreadReward\Created;
use Discuz\Base\DzqModel;
use Illuminate\Database\Eloquent\Model;
use Discuz\Foundation\EventGeneratorTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ThreadReward extends DzqModel
{
    use EventGeneratorTrait;

    protected $fillable = [
        'thread_id',
        'post_id',
        'type',
        'user_id',
        'answer_id',
        'money',
        'remain_money',
        'created_at',
        'updated_at',
        'expired_at'
    ];

    /**
     * Create a new self
     *
     * @param array $attributes
     * @return static
     */
    public static function build(array $attributes)
    {
        $self = new static;

        $self->fill($attributes);

        $self->raise(new Created($self));

        return $self;
    }

    /**
     * @return BelongsTo
     */
    public function beUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * 关联回答图片
     *
     * @return HasMany
     */
    public function images()
    {
        return $this->hasMany(Attachment::class, 'type_id')->where('type', Attachment::TYPE_OF_ANSWER)->orderBy('order');
    }

    public function getRewards($threadIds)
    {
        return self::query()->whereIn('thread_id', $threadIds)
            ->select([
                'thread_id as threadId',
                'type',
                'user_id as userId',
                'answer_id as answerId',
                'money',
                'remain_money as remainMoney',
                'expired_at as expiredAt'
            ])
            ->get()->pluck(null,'threadId')->toArray();
    }
}
