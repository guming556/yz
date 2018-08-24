<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectTypeToSubOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_order', function (Blueprint $table) {
            $table->tinyInteger('project_type')->unsigned()->default(0)->comment('1拆除 2水电 3防水 4泥工 5木工 6油漆 7综合');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_order', function (Blueprint $table) {
            $table->dropColumn('project_type');
        });
    }
}
