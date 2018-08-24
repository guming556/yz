<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableWorkOffer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_offer', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('work_id')->comment('关联kppw_work表id');
            $table->string('title')->comment('费用名称');
            $table->string('price')->comment('价钱');
            $table->string('actual_square')->comment('实际面积');
            $table->integer('status')->default(0)->comment('进程 0未开始 1设计师submit 2用户commit 3业主退回 4done');
            $table->engine = "InnoDB";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::drop('work_offer');
    }
}
