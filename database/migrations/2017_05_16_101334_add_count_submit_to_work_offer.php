<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountSubmitToWorkOffer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_offer', function (Blueprint $table) {
            $table->string('count_submit')->default(0)->comment('修改次数');
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
            $table->dropColumn('count_submit');
        });
    }
}
