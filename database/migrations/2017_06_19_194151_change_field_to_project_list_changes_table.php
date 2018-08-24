<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldToProjectListChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_list_changes', function (Blueprint $table) {
            $table->text('list_changes')->change()->default('')->comment('配置单更改内容');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_list_changes', function (Blueprint $table) {
            $table->dropColumn('list_changes');
        });
    }
}
