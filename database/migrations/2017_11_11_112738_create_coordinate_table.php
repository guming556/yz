<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoordinateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coordinate', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pid')->unsigned()->default(0)->comment('上级id');
            $table->string('shortname',32)->default('')->comment('简称');
            $table->string('name')->default('')->comment('名称');
            $table->string('merger_name')->default('')->comment('全称');
            $table->integer('level')->unsigned()->default(0)->comment('层级 0 1 2 省市区县');
            $table->string('pinyin',50)->default('')->comment('拼音');
            $table->string('code',20)->default('')->comment('长途区号');
            $table->string('zip_code',20)->default('')->comment('邮编');
            $table->string('first',5)->default('')->comment('首字母');
            $table->string('lat','20')->default('')->comment('纬度');
            $table->string('lng','20')->default('')->comment('经度');

//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('coordinate');
    }
}
