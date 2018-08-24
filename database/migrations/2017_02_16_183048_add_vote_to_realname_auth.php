<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVoteToRealnameAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('realname_auth', function (Blueprint $table) {

            $table->integer('experience')->comment('工作经验');
            $table->integer('user_type')->comment('2设计师 3管家 4监工');
            $table->string('lat','20')->default('')->comment('纬度');
            $table->string('lng','20')->default('')->comment('经度');
            $table->string('address')->default('')->comment('地址');

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
