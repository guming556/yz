<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTypeNameToCashoutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashout', function (Blueprint $table) {
            $table->string('user_type_name')->default('')->comment('用户种类名称');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashout', function (Blueprint $table) {
            $table->dropColumn('user_type_name');
        });
    }
}
