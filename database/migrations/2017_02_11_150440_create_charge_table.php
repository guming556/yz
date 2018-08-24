<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charge', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('工程名字');
            $table->text('content')->nullable()->comment('描述');
            $table->tinyInteger('listorder')->default(0)->comment('排序');
            $table->integer('pid')->default(0)->comment('一级分类默认为0');
            $table->tinyInteger('type')->default(1)->comment('1,设计师;2,管家;3监理');
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
        Schema::drop('charge');
    }
}
