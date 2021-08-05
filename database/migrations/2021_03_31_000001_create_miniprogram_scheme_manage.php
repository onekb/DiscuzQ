<?php
use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Query\Expression;

class createMiniprogramSchemeManage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->create('miniprogram_scheme_manage', function (Blueprint $table) {
            $table->id()->comment('id');
            $table->string('mini_app_id', 100)->comment('小程序appid');
            $table->string('scheme', 100)->comment('小程序全局scheme');
            $table->integer('expired_at')->useCurrent()->comment('过期时间');
            $table->integer('created_at')->useCurrent()->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->dropIfExists('miniprogram_scheme_manage');
    }
}
