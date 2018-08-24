<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUploadImgToWorkOffer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('work_offer', function (Blueprint $table) {
            $table->string('upload_status')->default(0)->comment('是否已上传了图纸，1为已上传');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_offer', function (Blueprint $table) {
            $table->dropColumn('upload_status');
        });
    }
}
