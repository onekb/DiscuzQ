<?php

use Discuz\Database\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;

class CreateThreadVoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('thread_votes', function (Blueprint $table) {
            $table->id()->comment('投票 id');
            $table->unsignedBigInteger('thread_id')->nullable(false)->comment('主题 id');
            $table->string('vote_title')->comment('投票标题');
            $table->tinyInteger('choice_type')->unsigned()->default(0)->comment('选择类型；1：单选、2：多选');
            $table->unsignedInteger('vote_users')->nullable(false)->default(0)->comment('参与人数');
            $table->timestamp('created_at')->default(new Expression('CURRENT_TIMESTAMP'))->comment('创建时间');
            $table->timestamp('updated_at')->default(new Expression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->timestamp('expired_at')->nullable()->comment('过期时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('thread_vote');
    }
}
