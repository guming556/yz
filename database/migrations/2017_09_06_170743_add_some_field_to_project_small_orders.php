<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldToProjectSmallOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_small_orders', function (Blueprint $table) {
            $table->integer('sub_order_id')->unsigned()->default(0)->comment('业主支付该订单的sub_order_id');
            $table->decimal('cash_house_keeper')->unsigned()->default(0)->comment('需要支付给管家的金额');
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
            $table->dropColumn('sub_order_id');
            $table->dropColumn('cash_house_keeper');
        });
    }
}
