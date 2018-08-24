<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDetailUrlToAuxiliary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auxiliary', function (Blueprint $table) {
            $table->string('detail_url')->default('')->comment('辅材包详细页地址');
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
            $table->dropColumn('detail_url');
        });
    }
}
