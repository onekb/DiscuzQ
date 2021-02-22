<?php

use Discuz\Database\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
/*
 * CREATE TABLE `admin_action_logs`
(
    `id`            bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '操作日志ID' ,
    `user_id`       bigint(20) UNSIGNED NOT NULL COMMENT '用户ID' ,
    `action_desc`   text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作描述' ,
    `ip`            varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ip地址' ,
    `created_at`    timestamp            DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COMMENT '管理员操作日志表';
 * */
class CreateAdminActionLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('admin_action_logs', function (Blueprint $table) {
            $table->id()->comment('操作日志ID');
            $table->unsignedBigInteger('user_id')->default(0)->index()->comment('用户ID');
            $table->text('action_desc')->comment('操作描述');
            $table->ipAddress('ip')->default('')->comment('ip地址');
            $table->timestamp('created_at')->default(new Expression('CURRENT_TIMESTAMP'))->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('admin_action_logs');
    }
}
