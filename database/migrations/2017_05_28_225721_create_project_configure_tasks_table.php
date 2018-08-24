<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectConfigureTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_configure_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('project_con_list')->comment('工程配置单');
            $table->integer('task_id')->comment('任务id');
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
        Schema::drop('project_configure_tasks');
    }
}
