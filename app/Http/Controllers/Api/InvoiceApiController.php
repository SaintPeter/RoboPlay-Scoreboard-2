<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\InvoiceReview;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\ {
	Team,
	Video,
	Invoices,
	CompYear
};

class InvoiceApiController extends Controller
{
	function invoice_json($year = 0) {
		if ($year == 0) {
			return response("Year Not Found", 404);
		}

		// Fetch Invoices and all data
		$invoicesCol = Invoices::with('user', 'school')
			->with(['videos' => function (HasMany $q) use ($year) {
				return $q->where('year', $year);
			}, 'videos.students'])
			->with(['teams' => function (HasMany $q) use ($year) {
				return $q->where('year', $year);
			}, 'teams.students', 'teams.students.math_level', 'teams.division'])
			->where('year', $year)
			->get();

		// Collapse collections down to simple JSON
		$teamData = [];
		$videoData = [];
		$invoices = $invoicesCol->reduce(function ($invoice_carry, $invoice) use (&$teamData, &$videoData) {
			// Count checked and unchecked teams
			$teamStats = $invoice->teams->reduce(function ($team_carry, $team) {
				$team_carry[$team->audit ? 'checked' : 'unchecked']++;
				return $team_carry;
			}, ['checked' => 0, 'unchecked' => 0]);

			// Count checked and unchecked videos
			$videoStats = $invoice->videos->reduce(function ($video_carry, $video) {
				$video_carry[$video->audit ? 'checked' : 'unchecked']++;
				return $video_carry;
			}, ['checked' => 0, 'unchecked' => 0]);

			// Teams to Array
			$team_student_count = 0;
			$teamData = $invoice->teams->reduce(function ($team_carry, $team) use (&$team_student_count, $invoice) {
				$student_list = $team->students->reduce(function ($student_carry, $student) {
					$student_carry[] = [
						'id' => $student->id,
						'name' => $student->fullName(),
						'math_level_name' => $student->math_level->name,
						'math_lavel' => $student->math_level->level,
						'tshirt' => $student->tshirt ? $student->tshirt : "N/A",
					];
					return $student_carry;
				}, []);
				$team_carry[$invoice->id][] = [
					'id' => $team->id,
					'name' => $team->name,
					'division_id' => $team->division_id,
					'student_count' => $team->students->count(),
					'status' => $team->audit,
					'students' => $student_list,
				];
				$team_student_count += $team->students->count();
				return $team_carry;
			}, $teamData);

			// Videos to Array
			$video_student_count = 0;
			$videoData = $invoice->videos->reduce(function ($video_carry, $video) use (&$video_student_count, $invoice) {
				$video_carry[$invoice->id][] = [
					'id' => $video->id,
					'name' => $video->name,
					'code' => $video->yt_code,
					'vid_division_id' => $video->vid_division_id,
					'student_count' => $video->students->count(),
					'status' => $video->audit,
					'notes' => $video->notes ? $video->notes : '',
				];
				$video_student_count += $video->students->count();
				return $video_carry;
			}, $videoData);

			// Each Invoice, grouped by ID
			$invoice_carry[$invoice->id] = [
				'id' => $invoice->id,
				'remote_id' => $invoice->remote_id,
				'user_id' => $invoice->user_id,
				'notes' => $invoice->notes,
				'paid' => $invoice->paid,
				'user_name' => $invoice->user->name,
				'email' => $invoice->user->email,
				'school_name' => $invoice->school->name,
				'team_count' => $invoice->team_count,
				'teams_checked' => $teamStats['checked'],
				'teams_unchecked' => $teamStats['unchecked'],
				'team_student_count' => $team_student_count,
				//'team_data' => $teamData,
				'video_count' => $invoice->video_count,
				'videos_checked' => $videoStats['checked'],
				'videos_unchecked' => $videoStats['unchecked'],
				'video_student_count' => $video_student_count,
				//'video_data' => $videoData,
			];
			return $invoice_carry;
		}, []);

		$comp_year = CompYear::where('year', $year)
			->with('vid_divisions', 'divisions', 'divisions.competition')
			->first();

		$vid_division_list = $comp_year->vid_divisions->pluck('name', 'id')->all();

		$division_list = [];
		foreach ($comp_year->divisions as $division) {
			$division_list[$division->competition->name][$division->id] = $division->longname();
		}

		$returnData = [
			'team_divisions' => $division_list,
			'vid_divisions' => $vid_division_list,
			'invoices' => $invoices,
			'team_data' => $teamData,
			'video_data' => $videoData,
		];

		return response()->json($returnData);
	}

	function sync_invoices($year) {
		$result = \Artisan::call('scoreboard:sync_db');
		if ($result == 0) {
			$output = App::make('InvoiceReview')->invoice_sync($year);
			return response()->json(['message' => $output]);
		}
		return response("Error", 500);
	}

	function save_team_division($team_id, $div_id) {
		$team = Team::findOrFail($team_id);
		$team->update(['division_id' => $div_id]);
		return 'true';
	}

	public function save_video_division($video_id, $vid_div_id) {
		$video = Video::findOrFail($video_id);
		$video->update(['vid_division_id' => $vid_div_id]);
		return 'true';
	}

	public function toggle_team($team_id) {
		$team = Team::findOrFail($team_id);
		$team->update(['audit' => !$team->audit]);
		return 'true';
	}

	public function update_paid_notes(Request $req, $invoice_id, $paid) {
		$invoice = Invoices::findOrFail($invoice_id);
		$invoice->update(['paid' => $paid, 'notes' => $req->input('notes', '')]);
		return 'true';
	}
}
