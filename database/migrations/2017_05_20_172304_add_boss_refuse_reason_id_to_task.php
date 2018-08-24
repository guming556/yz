<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBossRefuseReasonIdToTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task', function (Blueprint $table) {
            $table->tinyInteger('boss_refuse_reason_id')->unsigned()->default(0)->comment('用户深化和初步设计取消订单原因id,关联refuse_reason表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task', function (Blueprint $table) {
            $table->dropColumn('boss_refuse_reason_id');
        });
    }
}
