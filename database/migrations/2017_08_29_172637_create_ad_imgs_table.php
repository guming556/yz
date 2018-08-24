<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdImgsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_imgs', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type',['ad_img','help_img'])->default('ad_img')->comment('图片类型');
            $table->string('url')->default('')->comment('图片url');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ad_imgs');
    }
}
