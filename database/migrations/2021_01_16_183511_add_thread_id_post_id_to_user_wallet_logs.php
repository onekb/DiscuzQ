<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddThreadIdPostIdToUserWalletLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('user_wallet_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('thread_id')->nullable()->after('question_id')->comment('关联的threads主键ID');
            $table->unsignedBigInteger('post_id')->nullable()->after('thread_id')->comment('关联的posts主键ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('user_wallet_logs', function (Blueprint $table) {
            $table->dropColumn('thread_id');
            $table->dropColumn('post_id');
        });
    }
}
