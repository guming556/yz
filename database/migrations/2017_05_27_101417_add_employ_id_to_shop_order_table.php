<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmployIdToShopOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shop_order', function (Blueprint $table) {
            $table->integer('employ_id')->unsigned()->comment('订单主id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shop_order', function (Blueprint $table) {
            $table->dropColumn('employ_id');
        });
    }
}
