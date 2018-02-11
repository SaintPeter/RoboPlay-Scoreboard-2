<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentablesTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'studentables';

    /**
     * Run the migrations.
     * @table studentables
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('studentable_id');
            $table->string('studentable_type');

            $table->index(["student_id"], 'studentables_student_id_index');

            $table->index(["studentable_id"], 'studentables_studentable_id_index');
            $table->timestamps();


            $table->foreign('student_id', 'studentables_student_id_index')
                ->references('id')->on('students')
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
