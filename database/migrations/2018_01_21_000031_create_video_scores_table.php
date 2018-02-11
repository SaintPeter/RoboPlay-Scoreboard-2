<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoScoresTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'video_scores';

    /**
     * Run the migrations.
     * @table video_scores
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('vid_score_type_id');
            $table->unsignedInteger('video_id');
            $table->unsignedInteger('vid_division_id');
            $table->unsignedInteger('judge_id');
            $table->integer('score_group');
            $table->integer('s1')->default('0');
            $table->integer('s2')->default('0');
            $table->integer('s3')->default('0');
            $table->integer('s4')->default('0');
            $table->integer('s5')->default('0');
            $table->integer('s6')->default('0');
            $table->integer('s7')->default('0');
            $table->integer('s8')->default('0');
            $table->integer('s9')->default('0');
            $table->integer('s10')->default('0');
            $table->integer('total');
            $table->float('average');
            $table->float('norm_avg');

            $table->index(["vid_division_id"], 'video_scores_vid_division_id_foreign');

            $table->index(["video_id"], 'video_scores_video_id_foreign');
            $table->timestamps();


            $table->foreign('vid_division_id', 'video_scores_vid_division_id_foreign')
                ->references('id')->on('vid_divisions')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('video_id', 'video_scores_video_id_foreign')
                ->references('id')->on('videos')
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
