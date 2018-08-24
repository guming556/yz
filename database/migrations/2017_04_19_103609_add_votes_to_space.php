<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVotesToSpace extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('space', function (Blueprint $table) {
           $table->integer('is_deleted')->default(0)->comment('0为未删除 ， 1为已删除');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('space', function (Blueprint $table) {
            //
        });
    }
}
