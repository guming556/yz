<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('management', function (Blueprint $table) {
            $table->increments('id');
            $table->string('manage_id')->comment('账号');
            $table->string('pwd')->comment('密码');
            $table->string('name')->comment('姓名');
            $table->bigInteger('tel')->comment('手机');
            $table->bigInteger('qq')->comment('qq号');
            $table->string('email')->comment('邮箱');
            $table->string('job')->comment('职位');
            $table->integer('status')->comment('状态');
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
        Schema::drop('management');
    }
}
