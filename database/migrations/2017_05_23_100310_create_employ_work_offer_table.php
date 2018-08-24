<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployWorkOfferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employ_work_offer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('employ_work_id')->unsigned()->comment('关联kppw_employ_work表id');
            $table->tinyInteger('status')->comment('进程 0未开始 1设计师submit 2用户commit 3业主退回 4done');
            $table->tinyInteger('sn')->comment('步骤');
            $table->integer('employ_id')->unsigned()->comment('任务id,kppw_employ表id');
            $table->integer('from_uid')->unsigned()->comment('业主id');
            $table->integer('to_uid')->unsigned()->comment('工作人员id');
            $table->tinyInteger('count_submit')->unsigned()->comment('工作人员id');
            $table->string('title')->default('')->comment('流程节点 ，设计师报价');
            $table->decimal('actual_square',10,3)->comment('实际面积');
            $table->string('percent')->default('')->comment('价格百分比');
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
        Schema::drop('employ_work_offer');
    }
}
