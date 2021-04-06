<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPostedAtToThreads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('threads', function (Blueprint $table) {
            $table->dateTime('posted_at')->nullable()->after('location')->comment('最新评论时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
