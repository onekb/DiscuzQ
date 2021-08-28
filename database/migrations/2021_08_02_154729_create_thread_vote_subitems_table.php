<?php

use Discuz\Database\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;

class CreateThreadVoteSubitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('thread_vote_subitems', function (Blueprint $table) {
            $table->id()->comment('投票选项 id');
            $table->unsignedBigInteger('thread_vote_id')->nullable(false)->comment('投票主题 id');
            $table->string('content')->default('')->comment('内容');
            $table->unsignedInteger('vote_count')->nullable(false)->default(0)->comment('投票数量');
            $table->timestamp('created_at')->default(new Expression('CURRENT_TIMESTAMP'))->comment('创建时间');
            $table->timestamp('updated_at')->default(new Expression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('thread_vote_subitems');
    }
}
