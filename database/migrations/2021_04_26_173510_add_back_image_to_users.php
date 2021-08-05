<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddBackImageToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('users', function (Blueprint $table) {
            $table->string('background', 255)->default('')->after('avatar')->comment('背景图地址');
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
            $table->dropColumn('background');
        });
    }
}
