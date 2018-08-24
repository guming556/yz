<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDescToWorkOfferAppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_offer_applies', function (Blueprint $table) {
            $table->string('desc')->default('')->comment('描述');
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
            $table->dropColumn('desc');
        });
    }
}
