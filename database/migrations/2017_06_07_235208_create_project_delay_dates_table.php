<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectDelayDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_delay_dates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('task_id')->unsigned()->comment('任务id');
            $table->tinyInteger('sn')->unsigned()->comment('阶段id');
            $table->dateTime('end_date')->comment('管家提交的项目结束时间(工程延期单)');
            $table->dateTime('original_date')->comment('项目原定结束时间');
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
        Schema::drop('project_delay_dates');
    }
}
