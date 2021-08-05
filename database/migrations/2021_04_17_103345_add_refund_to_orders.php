<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRefundToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('orders', function (Blueprint $table) {
           $table->unsignedDecimal('refund', 10, 2)->default(0)->after('post_id')->comment('退款金额');
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
        $this->schema()->table('orders', function (Blueprint $table) {
            $table->dropColumn('refund');
            $table->dropColumn('return_at');
        });
    }
}