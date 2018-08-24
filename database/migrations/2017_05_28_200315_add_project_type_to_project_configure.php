<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectTypeToProjectConfigure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_configure_list', function (Blueprint $table) {
            $table->integer('project_type')->default(0)->comment('1拆除 2水电 3防水 4泥工 5木工 6油漆 7综合');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_configure', function (Blueprint $table) {
            $table->dropColumn('project_type');
        });
    }
}
