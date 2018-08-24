<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVotesToRealnameAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('realname_auth', function (Blueprint $table) {
            // $table->string('serve_area')->default('')->comment('服务区域');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('realname_auth', function (Blueprint $table) {
            //
        });
    }
}
