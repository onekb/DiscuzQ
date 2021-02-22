<?php

use Discuz\Database\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddParentidToCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->schema()->table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parentid')->default(0)->after('ip')->comment('所属一级分类的ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->schema()->table('categories', function (Blueprint $table) {
            $table->dropColumn('parentid');
        });
    }
}
