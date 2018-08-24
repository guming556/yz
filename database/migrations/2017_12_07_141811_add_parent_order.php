<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_log', function (Blueprint $table) {
            $table->string('housekeeper_task_id','20')->default(0)->comment('统一订单id，已管家任务id为基准');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_log', function (Blueprint $table) {
            //
        });
    }
}
