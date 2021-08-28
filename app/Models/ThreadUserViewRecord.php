<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $thread_id
 * @property int $user_id
 * @property Carbon $view_at
 * @package App\Models
 */
class ThreadUserViewRecord extends Model
{
    protected $table = 'thread_user_view_records';

    protected $fillable = ['thread_id', 'user_id', 'view_at'];

    public $incrementing = false;

    public $timestamps = false;
}