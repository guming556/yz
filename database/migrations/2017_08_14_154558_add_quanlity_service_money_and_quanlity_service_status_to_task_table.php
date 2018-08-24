<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuanlityServiceMoneyAndQuanlityServiceStatusToTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task', function (Blueprint $table) {
            $table->decimal('quanlity_service_money',10,2)->default(0)->comment('质保服务费用');
            $table->tinyInteger('quanlity_service_status')->default(0)->comment('质保服务费用支付状态:0 未付,1已付');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task', function (Blueprint $table) {
            $table->dropColumn('quanlity_service_money');
            $table->dropColumn('quanlity_service_money');
        });
    }
}
