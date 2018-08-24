<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecondOfferPriceToProjectWorkOfferChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_work_offer_changes', function (Blueprint $table) {
            $table->integer('offer_second_origin_price')->unsigned()->default(0)->comment('原阶段管家第二次报价,work_offer价格');
            $table->integer('offer_second_change_price')->unsigned()->default(0)->comment('该整改阶段管家第二次报价,work_offer价格');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_work_offer_changes', function (Blueprint $table) {
            $table->dropColumn('offer_second_origin_price');
            $table->dropColumn('offer_second_change_price');
        });
    }
}
