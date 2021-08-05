<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddShareCountToThreads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('threads', function (Blueprint $table) {
            $table->unsignedInteger('share_count')->nullable(false)->default(0)->after('paid_count')->comment('分享数');
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
