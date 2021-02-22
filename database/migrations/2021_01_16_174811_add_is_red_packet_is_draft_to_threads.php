<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddIsRedPacketIsDraftToThreads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('threads', function (Blueprint $table) {
            $table->tinyInteger('is_red_packet')->unsigned()->default(0)->after('is_display')->comment('是否添加红包，0未添加，1添加');
            $table->tinyInteger('is_draft')->unsigned()->default(0)->after('is_red_packet')->comment('是否为草稿，0不是，1是');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('threads', function (Blueprint $table) {
            $table->dropColumn('is_red_packet');
            $table->dropColumn('is_draft');
        });
    }
}
