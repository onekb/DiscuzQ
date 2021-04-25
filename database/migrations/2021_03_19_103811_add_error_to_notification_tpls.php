<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddErrorToNotificationTpls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('notification_tpls', function (Blueprint $table) {
            $table->tinyInteger('is_error')->default(0)->comment('模板是否配置错误');
            $table->text('error_msg')->nullable()->comment('错误信息');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('notification_tpls', function (Blueprint $table) {
            $table->dropColumn('is_error');
            $table->dropColumn('error_msg');
        });
    }
}
