<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\JudgeAwards;

class CreateJudgeAwardsTeamPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('judge_awards_team', function (Blueprint $table) {
            $table->integer('judge_awards_id')->unsigned()->index();
            $table->foreign('judge_awards_id')->references('id')->on('judge_awards')->onDelete('cascade');
            $table->integer('team_id')->unsigned()->index();
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->primary(['judge_awards_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('judge_awards_team');
    }
}
