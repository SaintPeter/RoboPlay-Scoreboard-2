<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRandomListsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'random_lists';

    /**
     * Run the migrations.
     * @table random_lists
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('format');
            $table->string('popup_format');
            $table->string('d1_format');
            $table->string('d2_format');
            $table->string('d3_format');
            $table->string('d4_format');
            $table->string('d5_format');
            $table->integer('display_order')->default('1');
            $table->unsignedInteger('challenge_id');

            $table->index(["challenge_id"], 'random_lists_challenge_id_foreign');
            $table->timestamps();


            $table->foreign('challenge_id', 'random_lists_challenge_id_foreign')
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
