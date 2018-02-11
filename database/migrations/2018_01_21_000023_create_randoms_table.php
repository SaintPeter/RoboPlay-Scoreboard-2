<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRandomsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'randoms';

    /**
     * Run the migrations.
     * @table randoms
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type')->default('single');
            $table->string('format');
            $table->integer('min1')->default('1');
            $table->integer('max1')->default('1');
            $table->integer('min2')->default('1');
            $table->integer('max2')->default('1');
            $table->tinyInteger('may_not_match')->default('0');
            $table->integer('display_order');
            $table->unsignedInteger('challenge_id');

            $table->index(["challenge_id"], 'randoms_challenge_id_foreign');
            $table->timestamps();


            $table->foreign('challenge_id', 'randoms_challenge_id_foreign')
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
