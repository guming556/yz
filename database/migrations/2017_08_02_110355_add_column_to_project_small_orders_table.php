<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToProjectSmallOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_small_orders', function (Blueprint $table) {
            $table->dateTime('change_date')->comment('管家提交的项目结束时间');
            $table->dateTime('original_date')->comment('项目原定结束时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_small_orders', function (Blueprint $table) {
            //
        });
    }
}
