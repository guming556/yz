<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSomeFieldToEmployTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employ', function (Blueprint $table) {
            $table->string('room_config', 32)->default('')->comment('房屋配置状况');
            $table->string('favourite_style', 50)->default('')->comment('喜好风格');
            $table->string('project_position', 11)->default(0)->comment('工程位置，关联project_position表的id');
            $table->integer('square')->unsigned()->default(0)->comment('面积');
            $table->tinyInteger('user_type')->unsigned()->default(0)->comment('限制哪种类型的服务人员接单 2：设计师 3：管家 4：监理  0:代表废单');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employ', function (Blueprint $table) {
            $table->dropColumn('room_config');
            $table->dropColumn('favourite_style');
            $table->dropColumn('project_position');
            $table->dropColumn('square');
            $table->dropColumn('user_type');
        });
    }
}
