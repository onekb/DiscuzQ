<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterBindTypeToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('users', function (Blueprint $table) {
            $table->tinyInteger('bind_type')->default(0)->comment('登录绑定类型；0：默认或微信；2：qq登录；');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('users', function (Blueprint $table) {
            $table->dropColumn('bind_type');
        });
    }
}
