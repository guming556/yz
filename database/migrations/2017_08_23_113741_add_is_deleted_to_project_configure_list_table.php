<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDeletedToProjectConfigureListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_configure_list', function (Blueprint $table) {
            $table->tinyInteger('is_deleted')->unsigned()->default(0)->comment('是否删除');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_configure_list', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });
    }
}
