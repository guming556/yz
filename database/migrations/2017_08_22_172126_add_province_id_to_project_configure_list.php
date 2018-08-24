<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProvinceIdToProjectConfigureList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_configure_list', function (Blueprint $table) {
            $table->integer('provice_id')->unsigned()->default(0)->comment('省份id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_configure_list', function (Blueprint $table) {
            $table->dropColumn('provice_id');
        });
    }
}
