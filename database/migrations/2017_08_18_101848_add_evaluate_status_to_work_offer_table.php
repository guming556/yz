<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEvaluateStatusToWorkOfferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_offer', function (Blueprint $table) {
            $table->tinyInteger('evaluate_status')->default(0)->comment('是否已经评价过0:未评价 1:已评价');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_offer', function (Blueprint $table) {
            $table->dropColumn('evaluate_status');
        });
    }
}
