<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProvinceIdToAuxiliaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auxiliary', function (Blueprint $table) {
            $table->smallInteger('province_id')->unsigned()->default(19)->comment('辅材包所选省份');
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
            $table->dropColumn('province_id');
        });
    }
}
