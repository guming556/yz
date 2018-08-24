<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLiveTvUrlToProjectPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_position', function (Blueprint $table) {
            $table->string('live_tv_url')->default('')->comment('视频直播地址');
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
            $table->dropColumn('live_tv_url');
        });
    }
}
