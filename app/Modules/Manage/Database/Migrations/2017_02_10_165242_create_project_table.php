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
        Schema::create('project', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('title')->comment('工程名字');
            $table->string('complete')->nullable()->comment('默认完成时间');
            $table->text('content')->nullable()->comment('描述');
            $table->integer('listorder')->default(0)->comment('排序');
            $table->integer('pid')->default(0)->comment('一级工程默认为0');
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
        //
    }
}