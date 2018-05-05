<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultsToVideoCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_comment', function (Blueprint $table) {
            $table->text('comment')->nullable()->default('')->change();
	        $table->text('resolution')->nullable()->default('')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_comment', function (Blueprint $table) {
	        $table->text('comment')->change();
	        $table->text('resolution')->change();
        });
    }
}
