<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkOfferApplyIdToProjectSmallOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_small_orders', function (Blueprint $table) {
            $table->integer('work_offer_apply_id')->unsigned()->default(0)->comment('work_offer_applyID');
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
            $table->dropColumn('work_offer_apply_id');
        });
    }
}
