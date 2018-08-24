<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOtherFieldToUserDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_detail', function (Blueprint $table) {
            $table->string('realname')->change()->default('')->comment('真实姓名');
            $table->string('nickname')->change()->default('')->comment('app端用户昵称');
            $table->string('qq')->change()->default('')->comment('用户qq');
            $table->string('wechat')->change()->default('')->comment('用户微信号');
            $table->string('card_number')->change()->default('')->comment('身份证号码');
            $table->string('city')->change()->default('')->comment('用户城市');
            $table->string('sign')->change()->default('')->comment('个人标签');
            $table->integer('province')->change()->comment('用户省份');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_detail', function (Blueprint $table) {
            $table->dropColumn('realname');
            $table->dropColumn('nickname');
            $table->dropColumn('qq');
            $table->dropColumn('wechat');
            $table->dropColumn('card_number');
            $table->dropColumn('city');
            $table->dropColumn('realname');
            $table->dropColumn('sign');
            $table->dropColumn('province');
        });
    }
}
