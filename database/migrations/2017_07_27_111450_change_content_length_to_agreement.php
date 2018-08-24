<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeContentLengthToAgreement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agreement', function (Blueprint $table) {
            $table->longText('content')->default('')->change()->comment('更改文本协议可存贮的长度');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agreement', function (Blueprint $table) {
            $table->dropColumn('content');
        });
    }
}
