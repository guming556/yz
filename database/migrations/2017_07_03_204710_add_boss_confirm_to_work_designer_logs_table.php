<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBossConfirmToWorkDesignerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_designer_logs', function (Blueprint $table) {
            $table->tinyInteger('boss_confirm')->default(0)->unsigned()->comment('业主是否选定了设计师,1选定,0未选定');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_designer_logs', function (Blueprint $table) {
            $table->dropColumn('boss_confirm');
        });
    }
}
