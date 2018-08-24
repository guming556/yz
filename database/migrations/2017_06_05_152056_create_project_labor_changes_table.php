<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectLaborChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_labor_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned()->comment('任务id');
            $table->integer('old_labor')->unsigned()->comment('原工人');
            $table->integer('new_labor')->unsigned()->comment('新工人,即替换后的工人');
            $table->integer('project_type')->unsigned()->comment('进行到哪个阶段');
            $table->integer('count_refuse')->unsigned()->comment('驳回次数');
            $table->text('list_detail')->comment('整改单详细');
            $table->tinyInteger('status')->unsigned()->comment('1:业主提交,2.管家提交整改单,3.驳回管家整改单,4.业主确认整改单,5.平台正在匹配,6.平台匹配完成');
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
        Schema::drop('project_labor_changes');
    }
}
