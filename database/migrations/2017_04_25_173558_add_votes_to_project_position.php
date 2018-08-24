<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVotesToProjectPosition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_position', function (Blueprint $table) {
            $table->string('room_config')->default(0)->comment('房屋配置');
            $table->integer('square')->default(0)->comment('工程面积');
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
            //
        });
    }
}
