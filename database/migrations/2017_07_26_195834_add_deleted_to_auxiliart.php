<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedToAuxiliart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auxiliary', function (Blueprint $table) {
            $table->integer('deleted')->unsigned()->default(0)->comment('1为已删除');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auxiliary', function (Blueprint $table) {
            Schema::drop('deleted');
        });
    }
}
