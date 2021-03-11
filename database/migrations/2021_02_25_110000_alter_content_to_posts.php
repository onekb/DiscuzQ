<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterContentToPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('posts', function (Blueprint $table) {
            $table->mediumtext('content')->nullable()->comment('内容')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('posts', function (Blueprint $table) {
            //
        });
    }
}
