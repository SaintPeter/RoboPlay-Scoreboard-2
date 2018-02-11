<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChallengeDivisionTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'challenge_division';

    /**
     * Run the migrations.
     * @table challenge_division
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('challenge_id');
            $table->unsignedInteger('division_id');
            $table->unsignedInteger('display_order');

            $table->index(["division_id"], 'challenge_division_division_id_index');

            $table->index(["challenge_id"], 'challenge_division_challenge_id_index');


            $table->foreign('challenge_id', 'challenge_division_challenge_id_index')
                ->references('id')->on('challenges')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('division_id', 'challenge_division_division_id_index')
                ->references('id')->on('divisions')
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
