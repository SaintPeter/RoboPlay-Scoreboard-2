<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesToCompTearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('comp_years', function (Blueprint $table) {
		    $table->date('reminder_start')->after('invoice_type_id')->default('1900-01-01');
		    $table->date('reminder_end')->after('reminder_start')->default('1900-01-01');
		    $table->date('edit_end')->after('reminder_end')->default('1900-01-01');
	    });

	    DB::update("UPDATE `comp_years` " .
		    "SET " .
            "reminder_start = STR_TO_DATE(CONCAT(year, '-03-22'), \"%Y-%m-%d\")," .
            "reminder_end = STR_TO_DATE(CONCAT(year, '-04-12'), \"%Y-%m-%d\")," .
            "edit_end = STR_TO_DATE(CONCAT(year, '-05-06'), \"%Y-%m-%d\")");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('comp_years', function (Blueprint $table) {
	        $table->dropColumn('reminder_start');
		    $table->dropColumn('reminder_end');
		    $table->dropColumn('edit_end');
	    });
    }
}
