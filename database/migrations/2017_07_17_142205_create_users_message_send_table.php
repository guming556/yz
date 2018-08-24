<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersMessageSendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_message_send', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->unsigned()->default(0)->comment('用户id');
            $table->text('message')->default('')->comment('消息内容');
            $table->tinyInteger('is_read')->default(0)->comment('是否已读 0未读,1已读');
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
        Schema::drop('users_message_send');
    }
}
