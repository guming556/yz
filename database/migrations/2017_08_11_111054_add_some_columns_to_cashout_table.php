<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnsToCashoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashout', function (Blueprint $table) {
            $table->string('work_offer_status_name')->default('')->comment('工地进行到那个阶段什么状态');
            $table->string('sn_title')->default('')->comment('提现哪个阶段的钱');
            $table->string('boss_phone_num')->default('')->comment('业主电话');
            $table->string('position_address')->default('')->comment('工地地址');
            $table->string('worker_phone_num')->default('')->comment('提现人的手机号码');
            $table->string('worker_name')->default('')->comment('提现人的真名');
            $table->string('bank_name')->default('')->comment('提现银行名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashout', function (Blueprint $table) {
            //
        });
    }
}
