<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsConfirmToProjectLaborChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_labor_changes', function (Blueprint $table) {
            $table->tinyInteger('is_confirm')->default(0)->comment('是否成功更换工人,0平台未更换,1已更换过');
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
            $table->dropColumn('is_confirm');
        });
    }
}
