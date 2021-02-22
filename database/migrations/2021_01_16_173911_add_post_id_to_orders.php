<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPostIdToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id')->default(0)->after('is_anonymous')->comment('关联的posts主键ID');
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
            $table->dropColumn('post_id');
        });
    }
}
