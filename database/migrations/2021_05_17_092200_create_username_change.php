<?php

use Discuz\Database\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;

class CreateUsernameChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('username_change', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedInteger('user_id')->index('user_id')->default(0)->comment('用户id');
            $table->integer('number')->unsigned()->nullable()->comment('次数');
            $table->dateTime('created_at')->comment('创建时间');
            $table->dateTime('updated_at')->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('username_change');
    }
}
