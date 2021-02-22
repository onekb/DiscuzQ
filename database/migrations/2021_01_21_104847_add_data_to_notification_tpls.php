<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDataToNotificationTpls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('notification_tpls', function (Blueprint $table) {
            $table->string('first_data')->default('')->comment('first.DATA');
            $table->text('keywords_data')->nullable()->comment('keywords.DATA');
            $table->string('remark_data')->default('')->comment('remark.DATA');
            $table->string('color')->default('')->comment('data color');
            $table->tinyInteger('redirect_type')->default(0)->comment('跳转类型：0无跳转 1跳转H5 2跳转小程序');
            $table->string('redirect_url')->default('')->comment('跳转地址');
            $table->string('page_path')->default('')->comment('跳转路由');
            $table->text('content')->nullable()->comment('内容')->change();
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
            $table->dropColumn('first_data');
            $table->dropColumn('keywords_data');
            $table->dropColumn('remark_data');
            $table->dropColumn('color');
            $table->dropColumn('redirect_type');
            $table->dropColumn('redirect_url');
            $table->dropColumn('page_path');
            $table->text('content')->comment('内容')->change();
        });
    }
}
