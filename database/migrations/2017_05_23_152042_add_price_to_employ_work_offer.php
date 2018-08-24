<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceToEmployWorkOffer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employ_work_offer', function (Blueprint $table) {
            $table->decimal('pay_to_user_cash', 10, 2)->unsigned()->default(0)->comment('约单价格');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employ_work_offer', function (Blueprint $table) {
            $table->dropColumn('pay_to_user_cash');
        });
    }
}
