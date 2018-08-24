<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExplainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('explain', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->comment('名字');
            $table->string('profile')->comment('简介');
            $table->text('content')->comment('内容');
            $table->string('editor')->comment('编辑人');
            $table->tinyInteger('deleted')->default(0)->comment('1,删除;');
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
        Schema::drop('explain');
    }
}
