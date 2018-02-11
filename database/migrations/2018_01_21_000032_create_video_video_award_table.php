<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoVideoAwardTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'video_video_award';

    /**
     * Run the migrations.
     * @table video_video_award
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable($this->set_schema_table)) return;
        Schema::create($this->set_schema_table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('video_id');
            $table->unsignedInteger('video_award_id');

            $table->index(["video_award_id"], 'video_video_award_video_award_id_index');

            $table->index(["video_id"], 'video_video_award_video_id_index');
            $table->timestamps();


            $table->foreign('video_award_id', 'video_video_award_video_award_id_index')
                ->references('id')->on('video_awards')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('video_id', 'video_video_award_video_id_index')
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
