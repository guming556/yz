<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectLogCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_log_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_log_id')->unsigned()->default(0)->comment('关联的project_log表主id');
            $table->integer('uid')->unsigned()->default(0)->comment('评论者');
            $table->enum('pass',[0,1])->default(1)->comment('是否通过,0未通过,1通过');
            $table->text('comments')->default('')->comment('评论内容');
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
        Schema::drop('project_log_comments');
    }
}
