<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideoReviewProblems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_review_problems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_id')->unsigned();
            $table->integer('reviewer_id');
            $table->tinyInteger('order');
            $table->integer('video_review_details_id');
            $table->integer('timestamp')->nullable();
            $table->text('comment');
            $table->boolean('resolved');
            $table->integer('resolver_id')->nullable();
            $table->timestamps();
            $table->foreign('video_id')
	            ->references('id')
	            ->on('videos')
	            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_review_problems');
    }
}
