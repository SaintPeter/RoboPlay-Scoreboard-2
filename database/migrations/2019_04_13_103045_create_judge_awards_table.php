<?php

use App\Models\JudgeAwards;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJudgeAwardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judge_awards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

	    JudgeAwards::insert([
		    [
			    'id' => 1,
			    'name' => 'Judge Spirit Award'
		    ],
		    [
			    'id' => 2,
			    'name' => 'Judge Teamwork Award'
		    ],
		    [
			    'id' => 3,
			    'name' => 'Judge Perseverance Award'
		    ],
	    ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('judge_awards');
    }
}
