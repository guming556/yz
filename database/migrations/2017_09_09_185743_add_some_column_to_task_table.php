<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeColumnToTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task', function (Blueprint $table) {
            $table->tinyInteger('hidden_status')->unsigned()->default(2)->comment('是否隐藏,1隐藏,2不隐藏');
            $table->integer('broadcastOrderBy')->unsigned()->default(0)->comment('直播排序');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task', function (Blueprint $table) {
            $table->dropColumn('hidden_status');
            $table->dropColumn('broadcastOrderBy');
        });
    }
}
