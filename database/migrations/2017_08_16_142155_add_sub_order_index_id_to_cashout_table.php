<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubOrderIndexIdToCashoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashout', function (Blueprint $table) {
            $table->integer('sub_order_index_id')->unsigned()->default(0)->comment('关联sub_order的主id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashout', function (Blueprint $table) {
            $table->dropColumn('sub_order_index_id');
        });
    }
}
