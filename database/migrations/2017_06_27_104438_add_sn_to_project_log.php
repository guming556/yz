<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSnToProjectLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_log', function (Blueprint $table) {
            $table->tinyInteger('sn')->unsigned()->comment('步骤节点');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_log', function (Blueprint $table) {
            $table->dropColumn('sn');
        });
    }
}
