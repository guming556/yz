<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level', function (Blueprint $table) {
            $table->increments('id');
            $table->string('offer_1')->comment('1星报价');
            $table->string('offer_2')->comment('2星报价');
            $table->string('offer_3')->comment('3星报价');
            $table->string('offer_4')->comment('4星报价');
            $table->string('offer_5')->comment('5星报价');
            $table->tinyInteger('type')->default(1)->comment('1管家,2监理,5泥水工,6木工,7水电工,8油漆工,9安装工,10拆除工');
            $table->string('upgrade')->comment('升级机制');
            $table->string('score')->comment('得分设置(冗余)');
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
        Schema::drop('level');
    }
}
