<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRubricTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'rubric';

    /**
     * Run the migrations.
     * @table rubric
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vid_score_type_id');
            $table->string('element');
            $table->string('element_name');
            $table->integer('order');
            $table->string('zero')->default('');
            $table->text('one');
            $table->text('two');
            $table->text('three');
            $table->text('four');
            $table->unsignedInteger('vid_competition_id');

            $table->index(["vid_competition_id"], 'rubric_vid_competition_id_foreign');
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
