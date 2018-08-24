<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnUsersMessageSendTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_message_send', function (Blueprint $table) {
            $table->string('title')->default('')->commnet('推送标题');
            $table->string('application')->default('')->commnet('推送种类');
            $table->integer('task_id')->default(0)->commnet('订单id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_message_send', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('application');
            $table->dropColumn('task_id');
        });
    }
}
