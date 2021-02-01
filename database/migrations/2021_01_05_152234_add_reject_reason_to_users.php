<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddRejectReasonToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('users', function (Blueprint $table) {
            $table->string('reject_reason', 100)->default('')->comment('审核拒绝原因')->after('register_reason');
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

        });
    }
}
