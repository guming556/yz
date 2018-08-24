<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldToCashoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashout', function (Blueprint $table) {
            $table->integer('task_id')->default(0)->comment('任务id');
            $table->smallInteger('sn')->default(0)->commnet('阶段id');
            $table->string('new_order')->default('')->comment('编号');
            $table->smallInteger('status')->default(0)->change()->comment('0 待审核 1 正在审核 2 审核通过 3 审核不通过 ');
            $table->decimal('total_pay_task', 10, 2)->default(0)->comment('该任务总计费用');
            $table->decimal('total_pay_task_actual', 10, 2)->default(0)->comment('该任务平台实际到账费用');
            $table->decimal('privilege_amount_sn', 10, 2)->default(0)->comment('该阶段抵用金');
            $table->decimal('privilege_amount_task', 10, 2)->default(0)->comment('该任务赠送金额');
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
            $table->dropColumn('task_id');
            $table->dropColumn('sn');
            $table->dropColumn('new_order');
            $table->dropColumn('total_pay_task');
            $table->dropColumn('total_pay_task_actual');
            $table->dropColumn('privilege_amount_sn');
            $table->dropColumn('privilege_amount_task');
        });
    }
}
