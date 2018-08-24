<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAllPriceToWorkOfferApplies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_offer_applies', function (Blueprint $table) {
            $table->decimal('all_price', 10, 2)->default(0)->comment('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_offer_applies', function (Blueprint $table) {
            //
        });
    }
}
