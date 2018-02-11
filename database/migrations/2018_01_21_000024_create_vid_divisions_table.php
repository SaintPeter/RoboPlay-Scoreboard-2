<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVidDivisionsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'vid_divisions';

    /**
     * Run the migrations.
     * @table vid_divisions
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description');
            $table->integer('display_order');
            $table->unsignedInteger('competition_id');

            $table->index(["competition_id"], 'vid_divisions_competition_id_foreign');
            $table->timestamps();


            $table->foreign('competition_id', 'vid_divisions_competition_id_foreign')
                ->references('id')->on('vid_competitions')
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
