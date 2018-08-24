<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSnToProjectLaborChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_labor_changes', function (Blueprint $table) {
            $table->tinyInteger('sn')->unsigned()->default(0)->comment('步骤id');
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
            $table->dropColumn('sn');
        });
    }
}
