<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('工程名字');
            $table->tinyInteger('complete')->default(0)->comment('默认完成时间');
            $table->text('content')->comment('描述');
            $table->tinyInteger('listorder')->default(0)->comment('排序');
            $table->tinyInteger('pid')->default(0)->comment('一级工程默认为0');
            $table->tinyInteger('deleted')->default(0)->comment('1,删除;');
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
        Schema::drop('project');
    }
}
