<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableVerification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_code', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('type')->comment('验证码性质，reg为注册 ，forget为忘记密码，其他后期自行补充');
            $table->string('code' , '6')->comment('验证码');
            $table->string('tel' , '11')->comment('手机号码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('verification_code');
    }
}
