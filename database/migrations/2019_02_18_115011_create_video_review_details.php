<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Video_review_details;

class CreateVideoReviewDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_review_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id');
            $table->tinyInteger('order');
            $table->string('reason');
            $table->string('cfp_section');
            $table->boolean('timestamp_required');
            $table->boolean('resolvable');
        });

	    Video_review_details::insert([[
		    'category_id' => 1,
		    'reason' => 'Missing Title Card',
		    'cfp_section' => 'III',
		    'order' => 1,
		    'resolvable' => true,
		    'timestamp_required' => false,
	        ],
		    [
			    'category_id' => 1,
			    'reason' => 'Title Card Missing Title',
			    'cfp_section' => 'III',
			    'order' => 2,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 1,
			    'reason' => 'Title Card Missing School Name',
			    'cfp_section' => 'III',
			    'order' => 3,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 1,
			    'reason' => 'Titles Exceed 5 seconds',
			    'cfp_section' => 'III',
			    'order' => 4,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 1,
			    'reason' => 'Intertitles Exceed 5 seconds each',
			    'cfp_section' => 'III',
			    'order' => 5,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 2,
			    'reason' => 'Missing Credits',
			    'cfp_section' => 'III',
			    'order' => 1,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 2,
			    'reason' => 'Credits Missing Musical Attribution/Permissions',
			    'cfp_section' => 'III',
			    'order' => 2,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 2,
			    'reason' => 'Credits Missing Script Filenames',
			    'cfp_section' => 'III',
			    'order' => 3,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 2,
			    'reason' => 'Credits Missing Student Names',
			    'cfp_section' => 'III',
			    'order' => 4,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 2,
			    'reason' => 'Credits Missing Teacher Name(s)',
			    'cfp_section' => 'III',
			    'order' => 5,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 2,
			    'reason' => 'Credits Missing School Name',
			    'cfp_section' => 'III',
			    'order' => 6,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 2,
			    'reason' => 'End Credits Exceed 15 seconds',
			    'cfp_section' => 'III',
			    'order' => 7,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 2,
			    'reason' => 'End Credits have Scrolling Text',
			    'cfp_section' => 'III',
			    'order' => 8,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 3,
			    'reason' => 'Missing Script',
			    'cfp_section' => 'V',
			    'order' => 1,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 3,
			    'reason' => 'Script Missing Dialog',
			    'cfp_section' => 'V',
			    'order' => 2,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 3,
			    'reason' => 'Script Missing Scene Descriptions',
			    'cfp_section' => 'V',
			    'order' => 3,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 3,
			    'reason' => 'Script Missing Stage Direction',
			    'cfp_section' => 'V',
			    'order' => 4,
			    'resolvable' => true,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 4,
			    'reason' => 'Lacks Substantial C-STEM Content',
			    'cfp_section' => 'III',
			    'order' => 5,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 4,
			    'reason' => 'Inappropriate material',
			    'cfp_section' => 'III',
			    'order' => 6,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 4,
			    'reason' => 'Bots are not primary actors',
			    'cfp_section' => 'III',
			    'order' => 8,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 4,
			    'reason' => 'Low overall quality',
			    'cfp_section' => 'III',
			    'order' => 9,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 4,
			    'reason' => 'Robotic Movement is Trivial',
			    'cfp_section' => 'III',
			    'order' => 10,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 5,
			    'reason' => 'Has Video not generated by Students',
			    'cfp_section' => 'III',
			    'order' => 1,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 5,
			    'reason' => 'Linkbots use Follow Mode',
			    'cfp_section' => 'VI',
			    'order' => 2,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ],
		    [
			    'category_id' => 5,
			    'reason' => 'Code not written by students',
			    'cfp_section' => 'VI',
			    'order' => 3,
			    'resolvable' => false,
			    'timestamp_required' => false,
		    ]]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_review_details');
    }
}
