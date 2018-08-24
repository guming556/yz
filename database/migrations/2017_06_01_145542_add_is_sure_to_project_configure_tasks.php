<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSureToProjectConfigureTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_configure_tasks', function (Blueprint $table) {
            $table->tinyInteger('is_sure')->default(0)->comment('业主是否确认配置单,0不确认,1确认');
            $table->integer('house_keeper_id')->comment('提交配置单的管家id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_configure_tasks', function (Blueprint $table) {
            $table->dropColumn('is_sure');
            $table->dropColumn('house_keeper_id');
        });
    }
}
