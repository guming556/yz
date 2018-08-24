<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCityIdToProjectConfigureTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_configure_tasks', function (Blueprint $table) {
            $table->smallInteger('city_id')->unsigned()->default(291)->comment('配置单所选城市');
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
            $table->dropColumn('city_id');
        });
    }
}
