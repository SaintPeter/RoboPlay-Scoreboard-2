<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Video_review_categories;

class CreateVideoReviewCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_review_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->tinyInteger('order');
        });

        Video_review_categories::insert([[
        		'name' => 'Titles',
		        'order' => 1
	        ],[
		        'name' => 'Credits',
		        'order' => 2
	        ],[
		        'name' => 'Script',
		        'order' => 3
	        ],[
		        'name' => 'Content',
		        'order' => 4
	        ],[
		        'name' => 'Disallowed Content',
		        'order' => 5
	        ]]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_review_categories');
    }
}
