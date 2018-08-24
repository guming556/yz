<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUploadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('upload', function (Blueprint $table) {
            $table->increments('id');
//            $table->dateTime('date')->comment('上传时间');
            $table->tinyInteger('order_id')->comment('订单号');
            $table->string('name')->comment('业主昵称');
            $table->string('phone','11')->comment('业主手机号');
            $table->string('addr')->comment('地区');
            $table->tinyInteger('status')->comment('状态');
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
        Schema::drop('upload');
    }
}
