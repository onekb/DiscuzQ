<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCommentIdsToPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('comment_post_id')->nullable()->after('reply_user_id')->comment('评论回复 id');
            $table->unsignedBigInteger('comment_user_id')->nullable()->after('comment_post_id')->comment('评论回复用户 id');
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
            $table->dropColumn('comment_post_id');
            $table->dropColumn('comment_user_id');
        });
    }
}
