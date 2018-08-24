<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVotesToEmploy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employ', function (Blueprint $table) {
            // $table->integer('project_position')->default(0)->comment('工程id');
            // $table->string('room_config')->default(0)->comment('房屋配置');
            // $table->string('favourite_style')->default(0)->comment('喜爱风格');
            // $table->integer('user_type')->default(0)->comment('接单人类型 2设计师 3管家 4监理');
            // $table->integer('square')->default(0)->comment('工程面积');
            // $table->integer('subscription')->default(0)->comment('预约金');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employ', function (Blueprint $table) {
            //
        });
    }
}
