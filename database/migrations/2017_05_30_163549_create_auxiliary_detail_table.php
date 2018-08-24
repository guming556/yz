<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuxiliaryDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auxiliary_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->string('brand',50)->comment('品牌');
            $table->string('model',50)->comment('产品型号');
            $table->string('spec',80)->comment('规格');
            $table->string('company',10)->comment('单位');
            $table->integer('num')->default(1)->comment('数量');
            $table->string('unit_price',10)->default(0)->comment('单价');
            $table->string('total',10)->default(0)->comment('总额');
            $table->integer('pid')->default(0)->comment('关联auxiliart表id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('auxiliary_detail');
    }
}
