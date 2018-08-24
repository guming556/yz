<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVotesToTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('task', function (Blueprint $table) {
            // $table->string('room_config')->default('')->comment('房屋配置');
            // $table->integer('favourite_style')->comment('喜好风格');
            // $table->tinyInteger('user_type')->comment('限制哪种类型的服务人员接单 2：设计师 3：管家 4：监理  0:代表废单');
            // $table->string('square' , '10')->default('0')->comment('产权面积');
            // $table->integer('project_position')->comment('工程位置，关联project_position表的id');
            // $table->string('poundage','10')->default('0')->comment('任务发布费');
            
            // $table->string('serve_area')->default('')->comment('服务区域');
            // $table->string('serve_area')->default('')->comment('服务区域');
            // $table->string('serve_area')->default('')->comment('服务区域');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('task', function (Blueprint $table) {
            //
        });
    }
}
