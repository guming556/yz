<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskCalculatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_calculators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stage')->default(0)->comment('阶段 1:施工;2:设计;3:管家;4:监理;5:辅材;6:主材(计算器)');
            $table->decimal('unit_price', 6, 2)->default(0)->comment('单价');
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
        Schema::drop('task_calculators');
    }
}
