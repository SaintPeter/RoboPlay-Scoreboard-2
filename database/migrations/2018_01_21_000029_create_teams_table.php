<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'teams';

    /**
     * Run the migrations.
     * @table teams
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('division_id');
            $table->unsignedInteger('school_id');
            $table->integer('teacher_id');
            $table->integer('year');
            $table->tinyInteger('audit')->default('0');

            $table->index(["division_id"], 'teams_division_id_foreign');
            $table->timestamps();


            $table->foreign('division_id', 'teams_division_id_foreign')
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
