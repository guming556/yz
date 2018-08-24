<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOriginalDateToProjectLaborChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_labor_changes', function (Blueprint $table) {
            $table->renameColumn('end_date','change_date')->comment('管家提交的项目结束时间');
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
        Schema::table('project_labor_changes', function (Blueprint $table) {
            $table->dropColumn('end_date');
            $table->dropColumn('original_date');
        });
    }
}
