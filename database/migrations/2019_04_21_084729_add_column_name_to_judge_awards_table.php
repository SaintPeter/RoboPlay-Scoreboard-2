<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\JudgeAwards;

class AddColumnNameToJudgeAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('judge_awards', function (Blueprint $table) {
            $table->string('col')->default('');
        });

        $awards = JudgeAwards::all();
        $update = [ 1 => 'spirit', 2 => 'teamwork', 3 => 'persevere'];
        foreach($awards as $award) {
        	$award->col = $update[$award->id];
        	$award->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('judge_awards', function (Blueprint $table) {
            $table->dropColumn('col');
        });
    }
}
