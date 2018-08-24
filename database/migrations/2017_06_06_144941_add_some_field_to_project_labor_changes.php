<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldToProjectLaborChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_labor_changes', function (Blueprint $table) {
            $table->tinyInteger('status_other')->unsigned()->comment('方案2(随时更换工人状态),1:业主提交,2.管家提交整改单,3.驳回管家整改单,4.业主确认整改单,5.管家提交延期单和变更单,6.监理确认延期单和变更单,7.业主驳回延期单和变更单,8.业主确认延期单和变更单,9.平台正在匹配,10.平台匹配完成');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_labor_changes', function (Blueprint $table) {
            $table->dropColumn('status_other');
        });
    }
}
