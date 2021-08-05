<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterSequences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('sequences', function (Blueprint $table) {
            $table->text('category_ids')->change();
            $table->text('group_ids')->change();
            $table->text('user_ids')->change();
            $table->text('topic_ids')->change();
            $table->text('thread_ids')->change();
            $table->text('block_user_ids')->change();
            $table->text('block_topic_ids')->change();
            $table->text('block_thread_ids')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('sequences', function (Blueprint $table) {
            $table->string('category_ids', 255)->change();
            $table->string('group_ids', 255)->change();
            $table->string('user_ids', 255)->change();
            $table->string('topic_ids', 255)->change();
            $table->string('thread_ids', 255)->change();
            $table->string('block_user_ids', 255)->change();
            $table->string('block_topic_ids', 255)->change();
            $table->string('block_thread_ids', 255)->change();
        });
    }
}
