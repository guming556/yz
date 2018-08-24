<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectPositionToProjectLaborChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_labor_changes', function (Blueprint $table) {
            $table->integer('project_position_id')->unsigned()->comment('工地id，关联project_position表的id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_labor_change', function (Blueprint $table) {
            $table->dropColumn('project_position_id');
        });
    }
}
