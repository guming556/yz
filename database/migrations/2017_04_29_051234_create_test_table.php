<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test', function (Blueprint $table) {
            $table->string('uuid')->default("")->comment('uuid');
        });

//        Schema::create('reply', function (Blueprint $table) {
//            $table->increments('id');
//            $table->string('keywords')->comment('关键词');
//            $table->text('conent')->comment('自动回复');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('test', function (Blueprint $table) {
            //
        });
    }
}
