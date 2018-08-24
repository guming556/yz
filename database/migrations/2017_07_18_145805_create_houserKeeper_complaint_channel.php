<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHouserKeeperComplaintChannel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('houserKeeper_complaint_channel', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned()->default(0)->comment('任务id');
            $table->string('sn')->default('')->comment('阶段');
            $table->integer('worker')->unsigned()->default(0)->comment('发起人');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态.0待审核,1审核通过,2审核驳回');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('houserKeeper_complaint_channel');
    }
}
