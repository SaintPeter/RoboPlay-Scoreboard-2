<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreRunsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'score_runs';

    /**
     * Run the migrations.
     * @table score_runs
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('run_number');
            $table->time('run_time');
            $table->string('scores');
            $table->integer('total');
            $table->integer('judge_id');
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('challenge_id');
            $table->unsignedInteger('division_id');
            $table->string('reason')->default('');

            $table->index(["challenge_id"], 'score_runs_challenge_id_foreign');

            $table->index(["division_id"], 'score_runs_division_id_foreign');

            $table->index(["team_id"], 'score_runs_team_id_foreign');
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('challenge_id', 'score_runs_challenge_id_foreign')
                ->references('id')->on('challenges')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('division_id', 'score_runs_division_id_foreign')
                ->references('id')->on('divisions')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('team_id', 'score_runs_team_id_foreign')
                ->references('id')->on('teams')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
     public function down()
     {
       Schema::dropIfExists($this->set_schema_table);
     }
}
