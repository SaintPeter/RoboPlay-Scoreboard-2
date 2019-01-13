<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYearIndexToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('videos', function (Blueprint $table) {
		    $table->index('year','idx_videos_year');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('videos', function (Blueprint $table) {
		    $table->dropIndex('idx_videos_year');
	    });
    }
}
