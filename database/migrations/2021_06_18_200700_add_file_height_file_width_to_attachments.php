<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddFileHeightFileWidthToAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('file_height')->default(0)->after('file_size')->comment('高度');
            $table->unsignedBigInteger('file_width')->default(0)->after('file_size')->comment('宽度');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('attachments', function (Blueprint $table) {
            $table->dropColumn('file_height');
            $table->dropColumn('file_width');
        });
    }
}
