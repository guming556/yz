<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('space', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('is_deleted')->default(0)->comment('0为未删除 ， 1为已删除');
            $table->string('name', '32')->comment('空间名称');
            $table->smallInteger('sort')->default(0)->comment('排序');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('space');
    }
}
