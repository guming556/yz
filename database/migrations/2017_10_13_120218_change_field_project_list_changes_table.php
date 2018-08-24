<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldProjectListChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_list_changes', function (Blueprint $table) {
            $table->string('old_labor')->default('')->change()->comment('原工人或者管家');
            $table->string('new_labor')->default('')->change()->comment('新工人或者管家');
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
            $table->dropColumn('old_labor');
            $table->dropColumn('new_labor');
        });
    }
}
