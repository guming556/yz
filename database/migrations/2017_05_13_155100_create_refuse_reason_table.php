<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefuseReasonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refuse_reason', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reason')->nullable()->comment('拒绝理由');
            $table->integer('is_deleted')->default(0)->comment('0为未删除 ， 1为已删除');
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
        Schema::drop('refuse_reason');
    }
}
