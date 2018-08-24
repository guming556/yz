<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWorkOfferIdToHouserkeeperComplaintChannel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('houserkeeper_complaint_channel', function (Blueprint $table) {
            $table->integer('work_offer_id')->default(0)->unsigned()->after('id')->comment('该阶段的主id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('houserkeeper_complaint_channel', function (Blueprint $table) {
            $table->dropColumn('work_offer_id');
        });
    }
}
