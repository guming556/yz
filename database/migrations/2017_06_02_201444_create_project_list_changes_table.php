<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectListChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_list_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('old_labor')->comment('原工人或者管家');
            $table->string('new_labor')->comment('新工人或者管家');
            $table->integer('handle_people')->unsigned()->comment('经手管家或监理');
            $table->integer('task_id')->unsigned()->comment('任务id');
            $table->string('list_changes')->comment('配置单更改内容');
            $table->tinyInteger('is_sure')->comment('是否确认(业主和监理的确认)');
            $table->decimal('pay_old_worker',10,2)->comment('支付给原工人或者管家的钱');
            $table->integer('project_type_id')->unsigned()->comment('做到哪个阶段的阶段id');
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
        Schema::drop('project_list_changes');
    }
}
