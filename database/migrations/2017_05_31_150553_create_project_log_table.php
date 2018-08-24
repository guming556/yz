<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
//    工程日志记录表
    public function up()
    {
        Schema::create('project_log', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('desc',255)->default('')->comment('文字说明');
            $table->integer('task_id')->default(1)->comment('任务id，关联task表id');
            $table->integer('project_position_id')->default(0)->comment('工地id，关联project_position表id');
            $table->text('img_serialize')->default('')->comment('序列化之后的图片');
            $table->integer('housekeeper_id')->default(0)->comment('上传者id，即管家id');
            $table->integer('project_type')->default(1)->comment('工程阶段 1拆除 2水电工程 3防水工程 4泥工工程 5木工工程 6油漆工程 7其他工程');
            $table->integer('stage')->default(0)->comment('是否需要验收 0不需要 1需要监理验收 2需要业主验收 3验收成功');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_log');
    }
}
