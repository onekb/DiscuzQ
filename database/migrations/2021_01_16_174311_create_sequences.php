<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
/*
 * CREATE TABLE `sequences` (
    `category_ids`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容分类ID' ,
    `group_ids`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户角色ID' ,
    `user_ids`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户ID' ,
    `topic_ids`         varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '话题ID' ,
    `thread_ids`        varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '主题ID/帖子' ,
    `block_user_ids`    varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '阻止显示的用户ID' ,
    `block_topic_ids`   varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '阻止显示的话题ID' ,
    `block_thread_ids`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '阻止显示的主题ID/帖子'
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COMMENT '智能排序表';
 * */
class CreateSequences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('sequences', function (Blueprint $table) {
            $table->string('category_ids')->default('')->comment('内容分类ID');
            $table->string('group_ids')->default('')->comment('用户角色ID');
            $table->string('user_ids')->default('')->comment('用户ID');
            $table->string('topic_ids')->default('')->comment('话题ID');
            $table->string('thread_ids')->default('')->comment('主题ID/帖子');
            $table->string('block_user_ids')->default('')->comment('阻止显示的用户ID');
            $table->string('block_topic_ids')->default('')->comment('阻止显示的话题ID');
            $table->string('block_thread_ids')->default('')->comment('阻止显示的主题ID/帖子');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('sequences');
    }
}
