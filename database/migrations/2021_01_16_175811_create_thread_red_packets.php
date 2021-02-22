<?php

use Discuz\Database\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
/*
 * CREATE TABLE `thread_red_packets` (
    `id`            bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '红包ID' ,
    `thread_id`     bigint(20) UNSIGNED NOT NULL COMMENT '关联的threads主键ID' ,
    `post_id`       bigint(20) UNSIGNED NOT NULL COMMENT '关联的posts主键ID' ,
    `rule`          tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发放规则，0定额，1随机' ,
    `condition`     tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '领取红包条件，0回复，1集赞' ,
    `likenum`       tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '若红包领取条件为集赞，必填集赞数' ,
    `money`         decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '红包总金额' ,
    `number`        int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '红包个数' ,
    `remain_money`  decimal(10,2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '剩余红包总额' ,
    `remain_number` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '剩余红包个数' ,
    `status`        tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0:红包已过期,1:红包未过期' ,
    `created_at`    timestamp DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at`    timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COMMENT '红包帖扩展表';
 * */
class CreateThreadRedPackets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('thread_red_packets', function (Blueprint $table) {
            $table->id()->comment('红包ID');
            $table->unsignedBigInteger('thread_id')->comment('关联的threads主键ID');
            $table->unsignedBigInteger('post_id')->comment('关联的posts主键ID');
            $table->tinyInteger('rule')->unsigned()->default(0)->comment('发放规则，0定额，1随机');
            $table->tinyInteger('condition')->unsigned()->default(0)->comment('领取红包条件，0回复，1集赞');
            $table->tinyInteger('likenum')->unsigned()->default(0)->comment('若红包领取条件为集赞，必填集赞数');
            $table->decimal('money', 10, 2)->unsigned()->default(0.00)->comment('红包总金额');
            $table->integer('number')->unsigned()->default(0)->comment('红包个数');
            $table->decimal('remain_money', 10, 2)->unsigned()->default(0.00)->comment('剩余红包总额');
            $table->integer('remain_number')->unsigned()->default(0)->comment('剩余红包个数');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('0:红包已过期,1:红包未过期');
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
        $this->schema()->dropIfExists('thread_red_packets');
    }
}
