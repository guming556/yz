<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnToHouserkeeperComplaint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('houserkeeper_complaint_channel', function (Blueprint $table) {
            $table->string('sn_title')->default('')->after('status')->comment('阶段名');
            $table->string('position_name')->default('')->after('status')->comment('工地名');
            $table->string('boss_phone_num')->default('')->after('status')->comment('业主电话');
            $table->string('house_phone_num')->default('')->after('status')->comment('管家电话');
            $table->string('visor_phone_num')->default('')->after('status')->comment('监理姓名');
            $table->string('boss_name')->default('')->after('status')->comment('业主姓名');
            $table->string('house_name')->default('')->after('status')->comment('管家姓名');
            $table->string('visor_name')->default('')->after('status')->comment('监理姓名');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('houserkeeper_complaint', function (Blueprint $table) {
            //
        });
    }
}
