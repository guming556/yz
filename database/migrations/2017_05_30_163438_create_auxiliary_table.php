<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuxiliaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auxiliary', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('price',10)->default(0)->comment('辅材包单价');
            $table->string('name',50)->commit('辅材包名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auxiliary');
    }
}
