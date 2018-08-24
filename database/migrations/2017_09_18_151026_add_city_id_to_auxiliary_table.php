<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCityIdToAuxiliaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auxiliary', function (Blueprint $table) {
            $table->smallInteger('city_id')->unsigned()->default(291)->comment('辅材包所选城市');
            $table->longText('content')->default('')->comment('辅材包详细,富文本');
            $table->dropColumn('deleted');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auxiliary', function (Blueprint $table) {
            $table->dropColumn('city_id');
            $table->dropColumn('content');
        });
    }
}
