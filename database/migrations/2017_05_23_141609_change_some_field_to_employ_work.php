<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSomeFieldToEmployWork extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employ_work', function (Blueprint $table) {
            $table->integer('employ_id')->unsigned()->default(0)->comment('约单任务主id');
            $table->integer('from_uid')->unsigned()->comment('业主id');
            $table->decimal('pay_to_user_cash', 10, 2)->unsigned()->default(0)->comment('约单价格');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employ_work', function (Blueprint $table) {
            $table->dropColumn('from_uid');
            $table->dropColumn('employ_id');
            $table->dropColumn('pay_to_user_cash');
        });
    }
}
