<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectWorkOfferChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_work_offer_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned()->comment('任务id');

            $table->tinyInteger('project_type')->unsigned()->default(0)->comment('阶段id,对应work_offer表的project_type');
            $table->tinyInteger('sn')->unsigned()->default(0)->comment('对应work_offer表的sn');

            $table->integer('offer_origin_price')->unsigned()->default(0)->comment('原阶段work_offer价格');
            $table->integer('offer_change_price')->unsigned()->default(0)->comment('该整改阶段work_offer价格');

            $table->integer('work_origin_price')->unsigned()->default(0)->comment('原阶段work价格');
            $table->integer('work_change_price')->unsigned()->default(0)->comment('该整改阶段work价格');

            $table->text('offer_origin_detail')->comment('原阶段详细');
            $table->text('offer_change_detail')->comment('该整改阶段详细');

            $table->text('task_origin_detail')->comment('任务原来的总配置单');
            $table->text('task_change_detail')->comment('该任务变更后的总配置单');

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
        Schema::drop('project_work_offer_changes');
    }
}
