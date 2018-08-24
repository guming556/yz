<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUserDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_detail', function (Blueprint $table) {
            $table->string('province')->default('')->change()->comment('用户省份');
            $table->string('area')->default('')->change()->comment('用户地区');
            $table->string('autograph')->default('')->change()->comment('个人签名');
            $table->integer('receive_task_num')->default(0)->change()->comment('承接任务数量');
            $table->integer('publish_task_num')->default(0)->change()->comment('发布任务数量');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_detail', function (Blueprint $table) {
            $table->dropColumn('province');
            $table->dropColumn('area');
            $table->dropColumn('autograph');
            $table->dropColumn('receive_task_num');
            $table->dropColumn('publish_task_num');
        });
    }
}
