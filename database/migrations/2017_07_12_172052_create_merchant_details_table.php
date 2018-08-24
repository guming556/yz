<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',32)->default('')->comment('联系人');
            $table->string('mobile')->unique()->default('')->comment('联系方式');
            $table->string('ad_slogan')->default('')->comment('广告语');
            $table->string('brand_name')->default('')->comment('品牌名称');
            $table->string('address')->default('')->comment('地址');
            $table->string('lat','20')->default('')->comment('纬度');
            $table->string('lng','20')->default('')->comment('经度');
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
        Schema::drop('merchant_details');
    }
}
