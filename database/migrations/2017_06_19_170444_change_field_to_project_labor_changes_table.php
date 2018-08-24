<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldToProjectLaborChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_labor_changes', function (Blueprint $table) {
            $table->string('old_labor')->change()->default('')->comment('原工人');
            $table->string('new_labor')->change()->default('')->comment('新工人,即替换后的工人');
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
            $table->dropColumn('old_labor');
            $table->dropColumn('new_labor');
        });
    }
}
