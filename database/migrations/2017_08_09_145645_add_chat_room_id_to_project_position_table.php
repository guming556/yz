<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChatRoomIdToProjectPositionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_position', function (Blueprint $table) {
            $table->string('chat_room_id')->default('')->comment('聊天室id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_position', function (Blueprint $table) {
            $table->dropColumn('chat_room_id');
        });
    }
}