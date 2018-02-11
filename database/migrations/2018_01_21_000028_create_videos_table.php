<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'videos';

    /**
     * Run the migrations.
     * @table videos
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('yt_code');
            $table->tinyInteger('has_custom');
            $table->unsignedInteger('vid_division_id');
            $table->integer('school_id');
            $table->integer('teacher_id');
            $table->tinyInteger('has_code');
            $table->tinyInteger('has_story')->default('0');
            $table->tinyInteger('has_choreo')->default('0');
            $table->tinyInteger('has_task')->default('0');
            $table->tinyInteger('has_vid');
            $table->integer('year');
            $table->integer('flag')->default('0');
            $table->tinyInteger('audit')->default('0');
            $table->text('notes')->nullable()->default(null);

            $table->index(["vid_division_id"], 'videos_vid_division_id_index');
            $table->timestamps();


            $table->foreign('vid_division_id', 'videos_vid_division_id_index')
                ->references('id')->on('vid_divisions')
                ->onDelete('cascade')
                ->onUpdate('no action');
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
