<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterNickNameToStopWords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('stop_words', function (Blueprint $table) {
            $table->string('nickname', 100)->default('')->after('username')->comment('用户昵称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('stop_words', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });
    }
}
