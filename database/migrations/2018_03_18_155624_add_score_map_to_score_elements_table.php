<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScoreMapToScoreElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('score_elements', function (Blueprint $table) {
            $table->string('score_map')->default('')->after('challenge_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('score_elements', function (Blueprint $table) {
        	$table->dropColumn('score_map');
        });
    }
}
