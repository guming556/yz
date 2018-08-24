<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFundStateToSubOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_order', function (Blueprint $table) {
            $table->smallInteger('fund_state')->default(1)->comment('资金动态,1支出,2收入');
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
            $table->dropColumn('fund_state');
        });
    }
}
