<?php

use Discuz\Database\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderChildren extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('order_children', function (Blueprint $table) {
            $table->id()->comment('自增id');
            $table->char('order_sn', 22)->default('')->comment('订单编号');
            $table->unsignedBigInteger('thread_id')->nullable(true)->default(0)->comment('关联的threads主键ID');
            $table->unsignedInteger('type')->nullable(false)->comment('订单类型：1注册，2打赏，3购买主题，4购买权限组，5付费提问，6问答围观，7购买附件，8站点付费，9红包，10悬赏，11合并订单');
            $table->unsignedInteger('status')->nullable(false)->default(1)->comment('订单状态：0待付款，1已付款，2取消，3支付失败，4过期，5部分退款，10全额退款，11异常订单');
           $table->unsignedDecimal('amount', 10, 2)->comment('金额');
           $table->unsignedDecimal('refund', 10, 2)->default(0)->comment('退款金额');
           $table->timestamp('return_at')->nullable(true)->comment('退款时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('order_children');
    }
}