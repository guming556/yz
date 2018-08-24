<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyBankInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_bank_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_name')->default('')->comment('公司名称');
            $table->string('bank_name')->default('')->comment('开户银行');
            $table->string('bank_account')->default(0)->comment('银行账号');
            $table->string('comment')->default('')->comment('备注');
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
        Schema::drop('company_bank_info');
    }
}
