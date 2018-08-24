<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenLiveTvStatusToProjectPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_position', function (Blueprint $table) {
            $table->smallInteger('open_live_tv_status')->default(0)->comment('是否对外开放');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_position', function (Blueprint $table) {
            $table->dropColumn('open_live_tv_status');
        });
    }
}
