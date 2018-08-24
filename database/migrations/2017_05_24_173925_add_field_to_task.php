<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToTask extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task', function (Blueprint $table) {
            $table->integer('housekeeperStar')->default(1)->comment('任务要求的管家星级');
            $table->integer('workerStar')->default(1)->comment('任务要求的工人星级 ');
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
            $table->dropColumn('housekeeperStar');
            $table->dropColumn('workerStar');
        });
    }
}
