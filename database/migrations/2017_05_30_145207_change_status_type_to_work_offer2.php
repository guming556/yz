<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStatusTypeToWorkOffer2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_offer', function (Blueprint $table) {
            $table->string('status',4)->change()->default(0)->comment('进程 0未开始 1工作端submit 1.5监理确认 2用户commit 3业主退回 3.5监理退回 4done');
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
            $table->dropColumn('status');
        });
    }
}
