<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHouseKeeperIdToProjectLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_log', function (Blueprint $table) {
            $table->integer('house_keeper_id')->unsigned()->default(0)->comment('管家id');
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
            $table->dropColumn('house_keeper_id');
        });
    }
}
