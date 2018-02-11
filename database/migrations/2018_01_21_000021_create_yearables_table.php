<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYearablesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'yearables';

    /**
     * Run the migrations.
     * @table yearables
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comp_year_id');
            $table->integer('yearable_id');
            $table->string('yearable_type');

            $table->index(["comp_year_id"], 'yearables_comp_year_id_foreign');
            $table->timestamps();


            $table->foreign('comp_year_id', 'yearables_comp_year_id_foreign')
                ->references('id')->on('comp_years')
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
