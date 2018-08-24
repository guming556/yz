<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToWorkAttachment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_attachment', function (Blueprint $table) {
            $table->smallInteger('img_type')->default(1)->comment('1初步图纸,2深化图纸');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_attachment', function (Blueprint $table) {
            $table->dropColumn('img_type');
        });
    }
}
