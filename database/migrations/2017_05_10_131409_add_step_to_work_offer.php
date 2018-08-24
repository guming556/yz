<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStepToWorkOffer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_offer', function (Blueprint $table) {
            $table->integer('sn')->default(0)->comment('步骤');
            $table->integer('task_id')->default(0)->comment('任务id');
            // $table->string('title')->default(0)->comment('流程节点 ，设计师报价');
            $table->string('percent')->default(0)->comment('0');
            $table->integer('from_uid')->default(0)->comment('业主id');
            $table->integer('to_uid')->default(0)->comment('工作人员id');
            // $table->integer('status')->default(0)->comment('进程 0未开始 1设计师submit 2用户commit 3done');
            $table->string('type')->default(0)->comment('designer');
        });
    }

    // 设计师报价
    // 生成订单，业主确认，支付完成

    // 初步设计
    // （上门，功能图，效果图），设计师提交，业主确认，确认完成

    // 深化

    // 

        // "id":0,
        //                                         "type":"designer",
        //                                         "task_id":0,      
        //                                         "sn" : 1,
        //                                         "title" : "初步设计方案",
        //                                         "percent":0.2
        //                                         "price":0
        //                                         "form_uid":0
        //                                         "to_uid":0
        //                                         "status":0 //(0未开始 1设计师submit 2用户commit 3done)


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_offer', function (Blueprint $table) {
            //
        });
    }
}
