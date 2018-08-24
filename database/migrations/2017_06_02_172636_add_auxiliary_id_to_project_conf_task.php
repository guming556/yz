<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuxiliaryIdToProjectConfTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_configure_tasks', function (Blueprint $table) {
            $table->integer('auxiliary_id')->default(0)->comment('选择的辅材包id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_configure_tasks', function (Blueprint $table) {
            $table->dropColumn('auxiliary_id');
        });
    }
}
