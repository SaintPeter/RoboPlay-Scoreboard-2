<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'students';

    /**
     * Run the migrations.
     * @table students
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name');
            $table->tinyInteger('nickname');
            $table->string('ssid', 12);
            $table->string('gender');
            $table->unsignedInteger('ethnicity_id');
            $table->integer('math_level_id');
            $table->string('tshirt', 4)->nullable()->default(null);
            $table->integer('grade');
            $table->string('email');
            $table->integer('year');
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('school_id');
            $table->timestamps();
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
