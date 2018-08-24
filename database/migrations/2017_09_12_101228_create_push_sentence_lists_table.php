<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePushSentenceListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_sentence_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('eng_name')->default('')->comment('该提示英文标示');
            $table->string('chn_name')->default('')->comment('该提示中文解释');
            $table->integer('nameBelongType')->unsigned()->default(0)->comment('该提示所属种类,10000段用作设计师,20000段管家,30000段,监理,40000段业主,50000杂项');
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
        Schema::drop('push_sentence_lists');
    }
}
