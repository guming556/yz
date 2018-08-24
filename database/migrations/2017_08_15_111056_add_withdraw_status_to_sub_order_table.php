<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWithdrawStatusToSubOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_order', function (Blueprint $table) {
            $table->smallInteger('withdraw_status')->default(0)->comment('提现状态,0未申请提现 1已申请提现');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_order', function (Blueprint $table) {
            $table->dropColumn('withdraw_status');
        });
    }
}
