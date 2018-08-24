<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkOfferAppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_offer_applies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned()->default(0)->comment('任务id');
            $table->integer('project_position')->unsigned()->default(0)->comment('工地id');
            $table->integer('sn')->unsigned()->default(0)->comment('步骤id');
            $table->integer('project_type')->unsigned()->default(0)->comment('工程种类id');
            $table->integer('labor')->unsigned()->default(0)->comment('工作者(工人)id');
            $table->integer('boss_id')->unsigned()->default(0)->comment('业主id');
            $table->integer('house_keeper_id')->unsigned()->default(0)->comment('管家id');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态:1.业主发起,2.管家提交该工程,3.业主驳回管家整改单,4.业主确认,5.业主付款,6.平台正在匹配,7.平台匹配完成,8.管家提交验收,9.业主驳回,10.业主确认,11.结算完成(新约单)');
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
        Schema::drop('work_offer_applies');
    }
}
