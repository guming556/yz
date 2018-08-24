<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectSmallOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_small_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned()->comment('任务id');
            $table->tinyInteger('project_type')->unsigned()->default(0)->comment('阶段id,对应work_offer表的project_type');
            $table->tinyInteger('sn')->unsigned()->default(0)->comment('对应work_offer表的sn');
            $table->integer('offer_change_price')->unsigned()->default(0)->comment('该整改阶段work_offer价格');
            $table->text('offer_change_detail')->comment('该整改阶段详细');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_small_orders');
    }
}
