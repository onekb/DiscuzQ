<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddNoticeIdToNotificationTpls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('notification_tpls', function (Blueprint $table) {
            $table->string('notice_id')->nullable()->after('id')->comment('模板唯一标识ID');
            // index
            $table->unique('notice_id', 'uk_notice_id');
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
            // index
            $table->dropUnique('uk_notice_id');
            $table->dropColumn('notice_id');
        });
    }
}
