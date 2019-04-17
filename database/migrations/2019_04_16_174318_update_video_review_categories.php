<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\ {
	Video_review_categories,
	Video_review_details
};

class UpdateVideoReviewCategories extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Video_review_categories::insert([
			[
				'name' => 'Meta',
				'order' => 6,
			],
		]);

		Video_review_details::insert([
			[
				'category_id' => 4,
				'reason' => 'Video longer than 5 minutes',
				'cfp_section' => 'III',
				'order' => 11,
				'resolvable' => false,
				'timestamp_required' => false,
			],
			[
				'category_id' => 4,
				'reason' => 'Video shorter than 1 minute',
				'cfp_section' => 'III',
				'order' => 12,
				'resolvable' => false,
				'timestamp_required' => false,
			], [
				'category_id' => 6,
				'reason' => 'Too few Students',
				'cfp_section' => 'III',
				'order' => 1,
				'resolvable' => false,
				'timestamp_required' => false,
			],
		]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Video_review_details::whereIn('reason', [
			'Video longer than 5 minutes',
			'Video shorter than 1 minute',
			'Too few Students'
		])->delete();

		Video_review_categories::where('name', 'Meta')->delete();
	}
}
