<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStatusToProjectLaborChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_labor_changes', function (Blueprint $table) {
            $table->string('status')->default('')->change()->comment('1:业主提交,2.管家提交整改单,3.业主驳回管家整改单,3.5,监理驳回管家整改单4.业主确认整改单,4.5监理确认整改单,5.平台正在匹配,6.平台匹配完成 ');
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
            $table->dropColumn('status');
        });
    }
}
