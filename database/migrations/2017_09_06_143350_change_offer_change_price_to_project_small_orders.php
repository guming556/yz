<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOfferChangePriceToProjectSmallOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_small_orders', function (Blueprint $table) {
            $table->decimal('offer_change_price')->change()->default(0)->comment('该小订单价格');
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
            $table->dropColumn('offer_change_price');
        });
    }
}
