<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeDetailToProjectSmallOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_small_orders', function (Blueprint $table) {
            $table->dropColumn('work_offer_apply_id');
            $table->string('small_order_id')->default('')->comment('订单id');
            $table->string('desc')->default('')->comment('描述');
            $table->integer('project_position')->unsigned()->default(0)->comment('工地id');
            $table->integer('labor')->unsigned()->default(0)->comment('工作者(工人)id');
            $table->integer('boss_id')->unsigned()->default(0)->comment('业主id');
            $table->integer('house_keeper_id')->unsigned()->default(0)->comment('管家id');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('状态:1.管家提交该工程,2.业主驳回管家整改单,3.业主付款,4.管家提交验收,5.业主确认,6.结算完成(新约单),7.异常结单');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_small_orders', function (Blueprint $table) {
            $table->dropColumn('small_order_id');
            $table->dropColumn('desc');
            $table->dropColumn('project_position');
            $table->dropColumn('project_type');
            $table->dropColumn('labor');
            $table->dropColumn('boss_id');
            $table->dropColumn('house_keeper_id');
            $table->dropColumn('status');
        });
    }
}
