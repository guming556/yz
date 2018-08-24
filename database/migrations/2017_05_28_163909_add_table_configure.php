<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTableConfigure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_configure_list', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('pid')->unsigned()->comment('父id');
            $table->string('name')->default('')->comment('工程项目');
            $table->string('cardnum')->default('')->comment('编号');
            $table->string('unit')->default('')->comment('单位');
            $table->integer('num')->default(1)->comment('数量');
            $table->string('desc')->default('')->comment('描述');
            $table->string('price')->default('')->comment('单价');
            $table->integer('city_id')->default(0)->comment('城市id');
            $table->string('work_type')->default('')->comment('工人种类id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('project_configure_list');
    }
}
