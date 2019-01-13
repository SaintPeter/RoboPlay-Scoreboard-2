<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\ {
	Invoices
};

class InvoiceApiController extends Controller
{
	function invoice_json($year = 0) {
		if($year == 0) {
			return response("Year Not Found",404);
		}

		$invoices = DB::table('invoices')
			->leftJoin('teams', function($join) use ($year){
				$join->on('teams.teacher_id', '=','invoices.user_id')
					->where('teams.year','=',$year);
			})
			->leftJoin('videos', function($join) use ($year){
				$join->on('videos.teacher_id', '=','invoices.user_id')
					->where('videos.year','=',$year);
			})
			->leftJoin('users', 'users.id','=','invoices.user_id')
			->leftJoin('schools','invoices.school_id','=','schools.id')
			->select('invoices.id',
				'invoices.remote_id',
				'invoices.user_id',
				'invoices.notes',
				'invoices.paid',
				'users.name as user_name',
				'users.email',
				'schools.name as school_name',
				'invoices.team_count',
				DB::Raw('CAST(ifnull(SUM(teams.audit = 0),0) AS UNSIGNED) AS teams_unchecked'),
				DB::Raw('CAST(ifnull(SUM(teams.audit = 1),0) AS UNSIGNED) AS teams_checked'),
				'invoices.video_count',
				DB::Raw('CAST(ifnull(SUM(videos.audit = 0),0) AS UNSIGNED) AS videos_unchecked'),
				DB::Raw('CAST(ifnull(SUM(videos.audit = 1),0) AS UNSIGNED) AS videos_checked')
			)
			->groupBy('invoices.id')
			->where('invoices.year','=', $year)
			->get();

		return response()->json($invoices);
	}
}
