<?php

use Discuz\Database\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

/*
 * create table `admin_sign_in_fields`
(
    `id`          bigint(20) unsigned not null auto_increment comment '自增id',
    `name`        varchar(20)         not null comment '用户端显示的字段名称',
    `type`        tinyint(4)          not null default 0 comment '0:单行文本框 1:多行文本框 2:单选 3:复选 4:图片上传 5:附件上传',
    `fields_ext`  text comment '字段扩展信息，Json表示选项内容',
    `fields_desc` text comment '字段介绍',
    `sort`        tinyint(4)          not null default 1 comment '自定义显示顺序',
    `status`      tinyint(4)          not null default 1 comment '0:废弃 1：启用',
    `created_at`  timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at`  timestamp           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    primary key (`id`)
) engine = InnoDB default charset = utf8mb4 comment '登录必填信息配置表';
 * */
class CreateAdminSignInFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('admin_sign_in_fields', function (Blueprint $table) {
            $table->unsignedBigInteger('id',true)->comment('自增id');
            $table->string('name', 20)->nullable(false)->comment('用户端显示的字段名称');
            $table->tinyInteger('type')->default(0)->comment('0:单行文本框 1:多行文本框 2:单选 3:复选 4:图片上传 5:附件上传');
            $table->text('fields_ext')->comment('字段扩展信息，Json表示选项内容');
            $table->text('fields_desc')->comment('字段介绍');
            $table->tinyInteger('sort')->default(1)->comment('自定义显示顺序');
            $table->tinyInteger('status')->default(1)->comment('-1:未启用 0:删除 1：启用');
            $table->tinyInteger('required')->default(1)->comment('是否必填项 0:否 1:是');
            $table->timestamp('created_at')->default(new Expression('CURRENT_TIMESTAMP'))->comment('创建时间');
            $table->timestamp('updated_at')->default(new Expression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('admin_sign_in_fields');
    }
}
