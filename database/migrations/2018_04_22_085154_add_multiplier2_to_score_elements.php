<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMultiplier2ToScoreElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('score_elements', function (Blueprint $table) {
            $table->decimal('multiplier2',10,5)->after('multiplier')->default(0.0);
            $table->decimal('multiplier',10,5)->default(0.0)->change();
            $table->boolean('enforce_limits')->default(false)->after('max_entry');
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
            $table->dropColumn('enforce_limits');
        	$table->dropColumn('multiplier2');
            $table->integer('multiplier')->default('0')->change();
        });
    }
}
