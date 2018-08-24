<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkDesignerLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_designer_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('old_uid')->default(0)->unsigned()->comment('原设计师');
            $table->integer('new_uid')->default(0)->unsigned()->comment('现设计师');
            $table->integer('task_id')->default(0)->unsigned()->comment('任务id');
            $table->tinyInteger('is_refuse')->default(0)->unsigned()->comment('1接受,2拒绝,3超时');
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
        Schema::drop('work_designer_logs');
    }
}
