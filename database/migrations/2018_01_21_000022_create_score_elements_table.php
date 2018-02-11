<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreElementsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'score_elements';

    /**
     * Run the migrations.
     * @table score_elements
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('display_text');
            $table->integer('element_number');
            $table->integer('base_value');
            $table->integer('multiplier');
            $table->integer('min_entry');
            $table->integer('max_entry');
            $table->string('type');
            $table->unsignedInteger('challenge_id');

            $table->index(["challenge_id"], 'score_elements_challenge_id_foreign');
            $table->timestamps();


            $table->foreign('challenge_id', 'score_elements_challenge_id_foreign')
                ->references('id')->on('challenges')
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
