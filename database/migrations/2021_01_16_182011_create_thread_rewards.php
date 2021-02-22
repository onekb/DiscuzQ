<?php

use Discuz\Database\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
/*
 * CREATE TABLE `thread_red_packets` (
    `id`            bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '悬赏帖ID' ,
    `thread_id`     bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '关联的threads主键ID' ,
    `post_id`       bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '关联的posts主键ID' ,
    `type`          tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0为所有人回答，1为指定人回答' ,
    `user_id`       bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户 id' ,
    `answer_id`     bigint(20) UNSIGNED NULL DEFAULT NULL COMMENT '被指定人ID，可为空' ,
    `money`         decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '悬赏金额' ,
    `remain_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '剩余的悬赏金额' ,
    `created_at`    timestamp DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at`    timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `expired_at`    datetime NOT NULL COMMENT '过期时间' ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COMMENT '悬赏帖扩展表';
 * */
class CreateThreadRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('thread_rewards', function (Blueprint $table) {
            $table->id()->comment('悬赏帖ID');
            $table->unsignedBigInteger('thread_id')->nullable()->comment('关联的threads主键ID');
            $table->unsignedBigInteger('post_id')->nullable()->comment('关联的posts主键ID');
            $table->tinyInteger('type')->unsigned()->default(0)->comment('0为所有人回答，1为指定人回答');
            $table->unsignedBigInteger('user_id')->default(0)->comment('用户 id');
            $table->unsignedBigInteger('answer_id')->nullable()->comment('被指定人ID，可为空');
            $table->decimal('money', 10, 2)->unsigned()->default(0.00)->comment('悬赏金额');
            $table->decimal('remain_money', 10, 2)->unsigned()->default(0.00)->comment('剩余的悬赏金额');
            $table->timestamp('created_at')->default(new Expression('CURRENT_TIMESTAMP'))->comment('创建时间');
            $table->timestamp('updated_at')->default(new Expression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->timestamp('expired_at')->nullable()->comment('过期时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('thread_rewards');
    }
}
