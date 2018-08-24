<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSureToProjectDelayDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_delay_dates', function (Blueprint $table) {
            $table->tinyInteger('is_sure')->default(0)->comment('业主是否确认延期单,0:管家没提交, 1:管家已提交,2:监理满意,3:监理驳回,4：业主驳回,5:业主确认');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_delay_dates', function (Blueprint $table) {
            $table->dropColumn('is_sure');
        });
    }
}
