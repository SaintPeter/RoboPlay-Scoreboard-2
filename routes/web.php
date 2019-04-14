<?php

use Illuminate\Support\Facades\ {
	Cache, Route
};
use Illuminate\Http\Request;
use App\Models\{
	CompYear, Files
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', [ 'as' => 'home', function(Request $req)
{
    $today = Carbon\Carbon::now()->setTimezone('America/Los_Angeles')->startOfDay();

    if($req->has('clear_cache')) {
    	Cache::clear('home');
    }

    $compyears = Cache::remember('home', 24 * 60, function () {
    	return CompYear::orderBy('year', 'desc')
		    ->with('competitions', 'competitions.divisions',
			    'vid_competitions')
		    ->get();
    });

    return View::make('home', compact('compyears', 'today'));
}]);

Route::get('file_viewer/{file_id}', [ 'as' => 'file_viewer', function($file_id) {
    $file = Files::find($file_id);
    if(file_exists($file->full_path())) {
        $source = file_get_contents($file->full_path());
        $geshi = new Geshi\Geshi($source, $file->filetype->language);
        $geshi->set_overall_style('white-space: pre-wrap;', true); // Force the pre tag to wrap in the container (iframe)
        echo $geshi->parse_code();
    } else {
        echo "ERROR: File '{$file->filename}' not found.";
    }
}]);


/* -----------------------------------------------------------------------------
|                             Public Routes                                     |
------------------------------------------------------------------------------*/
// Team Scores
Route::get('team/{team_id}', [ 'as' => 'display.teamscore', 'uses' => 'DisplayController@teamscore' ])
    ->where('team_id', '\d+');
Route::get('team/{team_id}/{with_judges}', [ 'as' => 'display.teamscore.judges', 'uses' => 'DisplayController@teamscore' ])
    ->where('team_id', '\d+');

// Single Competition
Route::get('comp/{competition_id}/{csv?}', [ 'as' => 'display.compscore', 'uses' => 'DisplayController@compscore' ])
    ->where('competition_id', '\d+')
    ->where('csv','csv');
Route::get('comp/top/{competition_id}/{csv?}', [ 'as' => 'display.compscore.top', 'uses' => 'DisplayController@compscore_top' ])
    ->where('competition_id', '\d+')
    ->where('csv','csv');

// Entire Competition Year
Route::get('compyear/{compyear_id}/{csv?}', [ 'as' => 'display.compyearscore', 'uses' => 'DisplayController@compyearscore' ])
    ->where('compyear_id', '\d+')
    ->where('csv','csv');
Route::get('compyear/top/{compyear_id}/{csv?}', [ 'as' => 'display.compyearscore.top', 'uses' => 'DisplayController@compyearscore_top' ])
    ->where('compyear_id', '\d+')
    ->where('csv','csv');

// Attempts
Route::get('attempts/{compyear_id}', [ 'as' => 'display.attempts', 'uses' => 'DisplayController@attempts' ]);

// All Scores
Route::get('all_scores/{compyear_id}', [ 'as' => 'display.all_scores', 'uses' => 'DisplayController@all_scores' ])
    ->where('compyear_id', '\d+');

// Paging Settings
Route::post('comp/{competition_id}/settings', [ 'as' => 'display.compsettings', 'uses' => 'DisplayController@compsettings' ])
    ->where('competition_id', '\d+');
Route::post('compyear/{compyear_id}/settings', [ 'as' => 'display.compyearsettings', 'uses' => 'DisplayController@compyearsettings' ])
    ->where('compyear_id', '\d+');
Route::post('all_scores/{compyear_id}/settings', [ 'as' => 'display.all_scores_settings', 'uses' => 'DisplayController@all_scores_settings' ])
    ->where('compyear_id', '\d+');
Route::post('attempts/{compyear_id}/settings', [ 'as' => 'display.attempts_settings', 'uses' => 'DisplayController@attempts_settings' ])
    ->where('compyear_id', '\d+');


Route::get('export_scores/{year}', [ 'as' => 'display.export_scores', 'uses' => 'DisplayController@export_year_scores']);

/* ----------------------- Video Display ---------------------------- */
Route::get('video_list/{comp_id}/{video_id}', [ 'as' => 'display.show_video', 'uses' => 'DisplayController@show_video'] )
    ->where('video_id', '\d+');
Route::get('video_list/{comp_id}/{winners?}', [ 'as' => 'display.video_list', 'uses' => 'DisplayController@video_list'] );

/* ------------------------- Ajax Handlers -------------------------- */
Route::get('ajax/blank_student/{index}', [ 'as' => 'ajax.blank_student', 'uses' => 'TeacherController@ajax_blank_student' ]);
Route::post('ajax/student_list/{type}/{teacher_id?}',	[ 'as' => 'ajax.student_list',  		'uses' => 'TeacherController@ajax_student_list' ]);
Route::post('ajax/load_students/{index}',  				[ 'as' => 'ajax.load_students',  		'uses' => 'TeacherController@ajax_load_students' ]);
Route::post('ajax/import_students_csv',  				[ 'as' => 'ajax.import_students_csv',	'uses' => 'TeacherController@ajax_import_students_csv' ]);

/* ------------------------------ User ------------------------------------- */
// All items in this route group require login

/** @noinspection PhpParamsInspection */
Route::middleware([ 'auth' ])->group( function() {

    Route::get('testlogin', function() {
        return "Logged in! - " . Auth::user()->user_email;
    });

    // Logout Page
    Route::get('logout', [ 'as' => 'logout', function() {
        Auth::logout();
        return Redirect::to('/');
    }]);

	/* -----------------------------------------------------------------------------
	|                              Admin Routes                                     |
	------------------------------------------------------------------------------*/
	/** @noinspection PhpParamsInspection */
    Route::middleware([ 'adminFilter' ])->group( function () {
        // Export Functions
        Route::get('challenge_students_csv', [ 'uses' => 'DisplayController@challenge_students_csv' ] );
        Route::get('video_students_csv', [ 'uses' => 'DisplayController@video_students_csv' ] );

        // Manage CompYears
        Route::resource('compyears', 'CompYearsController');
        Route::get('compyears/clear_div_scores/{compyear_id}', ['as' => 'compyears.clear_div_scores', 'uses' => 'DivisionsController@clear_compyear_scores']);

        // Manage Competitions
        Route::resource('competitions', 'CompetitionsController');
        Route::get('competitions/toggle_frozen/{competition_id}', [ 'as' => 'competition.toggle_frozen', 'uses' => 'CompetitionsController@toggle_frozen' ]);
        Route::get('competitions/freeze/all', [ 'as' => 'competitions.freeze.all', 'uses' => 'CompetitionsController@freeze_all' ]);
        Route::get('competitions/unfreeze/all', [ 'as' => 'competitions.unfreeze.all', 'uses' => 'CompetitionsController@unfreeze_all' ]);
        Route::get('competitions/toggle_active/{competition_id}', [ 'as' => 'competition.toggle_active', 'uses' => 'CompetitionsController@toggle_active' ]);

        // Display Competition Scores Unfrozen
//		Route::get('comp/{competition_id}/{do_not_freeze}', [ 'as' => 'display.compscore.do_not_freeze', 'uses' => 'DisplayController@compscore' ])
//		 ->where('competition_id', '\d+');

        // Manage Challenges
        Route::get('challenges/clear_cache', ['as' => 'challenges.clear_cache', 'uses' => 'ChallengesController@clear_cache']);
        Route::get('challenges/{id}/duplicate', [ 'as' => 'challenges.duplicate', 'uses' => 'ChallengesController@duplicate' ]);
        Route::resource('challenges', 'ChallengesController');

        // Manage Divisions
        Route::resource('divisions', 'DivisionsController');
        Route::get('divisions/assign/{division_id}', [
            'as' => 'divisions.assign',
            'uses' => 'DivisionsController@assign' ]);
        Route::get('divisions/{division_id}/clear_scores', [
            'as' => 'divisions.clear_scores',
            'uses' => 'DivisionsController@clear_scores' ])
            ->where('division_id', '\d+');
        Route::get('divisions/clear/all_scores', [
            'as' => 'divisions.clear_all_scores',
            'uses' => 'DivisionsController@clear_all_scores' ]);
        Route::post('divisions/saveassign', [
            'as' => 'divisions.saveassign',
            'uses' => 'DivisionsController@saveassign' ]);
        Route::get('divisions/{division_id}/remove/{challenge_id}', [
            'as' => 'divisions.removeChallenge',
            'uses' => 'DivisionsController@removeChallenge' ]);
        Route::post('divisions/copy/{from_id}', [ 'as' => 'divisions.copyChallenges', 'uses' => 'DivisionsController@copyChallenges' ] );
        Route::get('divisions/clear/{division_id}', [ 'as' => 'divisions.clearChallenges', 'uses' => 'DivisionsController@clearChallenges' ] );
        Route::post('divisions/updateChallengeOrder/{division_id}', [
            'as' => 'divisions.updateChallengeOrder',
            'uses' => 'DivisionsController@updateChallengeOrder' ]);

        //  Manage Video Competitions
        Route::resource('vid_competitions', 'Vid_competitionsController');
        Route::get('vid_competitions/user_list/{filter}', ['uses' => 'Vid_competitionsController@user_list']);
        Route::resource('vid_divisions', 'Vid_divisionsController');

        Route::resource('teams', 'TeamsController');
        Route::resource('score_elements', 'Score_elementsController');
        Route::get('score_elements/{challenge_id}/create', [ 'as' => 'score_elements.create',	'uses' => 'Score_elementsController@create']);
        Route::resource('randoms', 'RandomsController');
        Route::get('randoms/{challenge_id}/create', 	   [ 'as' => 'randoms.create',			'uses' => 'RandomsController@create']);
        Route::resource('random_list', 'RandomListsController');
        Route::get('random_list/{challenge_id}/create', 	[ 'as' => 'random_list.create',			'uses' => 'RandomListsController@create']);
        Route::get('list_elements/{random_list_id}/edit', 	[ 'as' => 'list_elements.edit',	'uses' => 'RandomListsController@edit_list_elements']);
        Route::post('list_elements/{random_list_id}/save', 	[ 'as' => 'list_elements.save',	'uses' => 'RandomListsController@save_list_elements']);
        Route::get('list_elements/{random_list_id}/show', 	[ 'as' => 'list_elements.show',	'uses' => 'RandomListsController@show_list_elements']);
        Route::resource('score_runs', 'Score_runsController');

	    // Admin Video Management
        Route::resource('videos', 'VideosController');
	    Route::get('videos/uploader/{video_id}',            [  'as' => 'videos.uploader',    'uses' => 'UploadController@index' ]);

        // Admin Stuff
	    Route::get('switch_user/{user_id}',					[ 'as' => 'switch_user',  	'uses' => 'AdminController@switch_user']);
	    Route::get('list_users',							[ 'as' => 'list_users', 	'uses' => 'AdminController@list_users']);
	    Route::get('admin/create_user',                     [ 'as' => 'create_user',    'uses' => 'AdminController@create_user']);
	    Route::post('admin/store_user',                     [ 'as' => 'store_user',     'uses' => 'AdminController@store_user']);
	    Route::get('admin/edit_user/{user}',                [ 'as' => 'edit_user',      'uses' => 'AdminController@edit_user']);
	    Route::post('admin/update_user/{user}',             [ 'as' => 'update_user',    'uses' => 'AdminController@update_user']);
	    Route::get('admin/delete_user/{user_id}',           [ 'as' => 'delete_user',    'uses' => 'AdminController@delete_user']);
	    Route::get('admin/create_users',                    [ 'as' => 'create_users',   'uses' => 'AdminController@create_users']);
	    Route::post('admin/store_users',                    [ 'as' => 'store_users',    'uses' => 'AdminController@store_users']);;

	    Route::get('admin/reset_password/{user}',           [ 'as' => 'admin_reset_password',    'uses' => 'AdminController@reset_password']);

        // Invoice Review
	    // React
	    Route::get('invoicer/{year?}',                      [ 'as' => 'invoicer', 'uses' => 'InvoiceReview@invoicer' ] )
		    ->where('year',"\d{4}");

	    /** @noinspection PhpParamsInspection */
	    Route::prefix('/api/invoicer/')->namespace('Api')->name('api.')->group(function() {
		    Route::get('invoice_list/{year}',                  'InvoiceApiController@invoice_json');
		    Route::get('save_team_div/{team_id}/{div_id}',     'InvoiceApiController@save_team_division');
		    Route::get('toggle_team/{id}',                     'InvoiceApiController@toggle_team');
		    Route::get('toggle_video/{id}',                    'InvoiceApiController@toggle_video');
		    Route::get('save_video_div/{video_id}/{div_id}',   'InvoiceApiController@save_video_division');
		    Route::get('sync_invoices/{year}',                 'InvoiceApiController@sync_invoices');
		    Route::get('last_sync_date/{year}',                'InvoiceApiController@last_sync_date');
		    Route::post('update_paid_notes/{id}/{paid}',       'InvoiceApiController@update_paid_notes');
		    Route::post('update_video_notes/{video_id}',       'InvoiceApiController@update_video_notes');
	    });

	    // Legacy
        Route::get('invoice_review/toggle_video/{id?}',     [ 'as' => 'invoice_review.toggle_video', 'uses' => 'InvoiceReview@toggle_video' ]);
        Route::get('invoice_review/toggle_team/{id?}',      [ 'as' => 'invoice_review.toggle_team',  'uses' => 'InvoiceReview@toggle_team' ]);
        Route::get('invoice_review/toggle_paid/{id?}',      [ 'as' => 'invoice_review.toggle_paid',  'uses' => 'InvoiceReview@toggle_paid' ]);
        Route::get('invoice_review/clear_paid/{id?}',       [ 'as' => 'invoice_review.clear_paid',  'uses' => 'InvoiceReview@clear_paid' ]);
        Route::post('invoice_review/set_paid/{id?}',        [ 'as' => 'invoice_review.set_paid',  'uses' => 'InvoiceReview@set_paid' ]);
        Route::get('invoice_review/save_video_div/{video_id?}/{div_id?}',  [ 'as' => 'invoice_review.save_video_div', 'uses' => 'InvoiceReview@save_video_division' ]);
        Route::get('invoice_review/save_team_div/{team_id?}/{div_id?}',    [ 'as' => 'invoice_review.save_team_div', 'uses' => 'InvoiceReview@save_team_division' ]);
        Route::post('invoice_review/save_video_notes/{id?}',      [ 'as' => 'invoice_review.save_video_notes', 'uses' => 'InvoiceReview@save_video_notes' ]);
        Route::get('invoice_review/{year?}/{terse?}',     	[ 'as' => 'invoice_review',		         'uses' => 'InvoiceReview@invoice_review' ])
            ->where('year', '\d{4}');
        Route::get('invoice_sync/{year}',					[ 'as' => 'invoice_sync',		         'uses' => 'InvoiceReview@invoice_sync' ]);
        Route::get('school_sync',					        [ 'as' => 'school_sync',		         'uses' => 'InvoiceReview@school_sync' ]);

        Route::get('data_export/{year?}',					[ 'as' => 'data_export',    'uses' => 'InvoiceReview@data_export' ])
            ->where('year', '\d{4}');
        Route::get('data_export/student_tshirts_{year}.csv',		[ 'as' => 'data_export.student_tshirts',    'uses' => 'InvoiceReview@student_tshirts_csv' ]);
        Route::get('data_export/teacher_tshirts_{year}.csv',		[ 'as' => 'data_export.teacher_tshirts',    'uses' => 'InvoiceReview@teacher_tshirts_csv' ]);
        Route::get('data_export/teacher_teams_{year}.csv',			[ 'as' => 'data_export.teacher_teams',    'uses' => 'InvoiceReview@teacher_teams_csv' ]);
        Route::get('data_export/student_demographics_{year}.csv',	[ 'as' => 'data_export.student_demographics',    'uses' => 'InvoiceReview@student_demographics_csv' ]);
        Route::get('data_export/video_demographics_{year}.csv',	    [ 'as' => 'data_export.video_demographics',    'uses' => 'InvoiceReview@video_demographics_csv' ]);
        Route::get('data_export/challenge_runs_{year}.csv',         [ 'as' => 'data_export.challenge_runs',    'uses' => 'InvoiceReview@challenge_runs_csv' ]);

        // File Types Editing
        Route::resource('filetypes', 'FileTypesController');
        Route::get('filetypes/add/{type?}',          [ 'as' => 'filetypes.add',     'uses' => 'FileTypesController@create' ]);
        /*
        Route::post('filetypes/store',        [ 'as' => 'filetypes.store',     'uses' => 'FileTypesController@store' ]);
        Route::post('filetypes/update/{id}',  [ 'as' => 'filetypes.update',    'uses' => 'FileTypesController@update' ]);
        */

        // Video Scores management
        Route::get('video_scores/{year?}', [
            'as' => 'video_scores.manage.index',
            'uses' => 'VideoManagementController@index' ])
            ->where('year', '\d{4}');
        Route::get('video_scores/reported/{year?}', [
            'as' => 'video_scores.manage.reported',
            'uses' => 'VideoManagementController@reported_videos' ])
            ->where('year', '\d{4}');
        Route::get('video_scores/by_video/{year?}', [
            'as' => 'video_scores.manage.by_video',
            'uses' => 'VideoManagementController@by_video' ])
            ->where('year', '\d{4}');
        Route::post('video_scores/process', [
            'as' => 'video_scores.manage.process',
            'uses' => 'VideoManagementController@process' ]);
        Route::post('video_scores/process_report', [
            'as' => 'video_scores.manage.process_report',
            'uses' => 'VideoManagementController@process_report' ]);
        Route::get('video_scores/unresolve/{comment_id}', [
            'as' => 'video_scores.manage.unresolve',
            'uses' => 'VideoManagementController@unresolve' ]);
        Route::get('video_scores/scores_csv/{year?}', [
            'as' => 'video_scores.manage.scores_csv',
            'uses' => 'VideoManagementController@scores_csv' ])
            ->where('year', '\d{4}');
        Route::get('video_scores/summary/{year?}', [
            'as' => 'video_scores.manage.summary',
            'uses' => 'VideoManagementController@summary' ])
            ->where('year', '\d{4}');
        Route::get('video_scores/judge_performance/{year?}', [
            'as' => 'video_scores.manage.judge_performance',
            'uses' => 'VideoManagementController@judge_performance' ])
            ->where('year', '\d{4}');
        Route::get('video_scores/graphs/{year?}', [
            'as' => 'video_scores.manage.graphs',
            'uses' => 'VideoManagementController@graphs' ])
            ->where('year', '\d{4}');

        // Graph Generation Routines
        Route::get('video_scores/video_performance_{year}.jpg', [
            'as' => 'graph_video_performace',
            'uses' => 'VideoManagementController@graph_video_scoring' ]);
        Route::get('video_scores/judge_performance_{year}.jpg', [
            'as' => 'graph_judge_performace',
            'uses' => 'VideoManagementController@graph_judge_scoring' ]);

        // Schedule Editing
        Route::get('schedule', [ 'as' => 'schedule.index', 'uses' => 'ScheduleController@index' ]);
        Route::post('schedule', [ 'as' => 'schedule.update', 'uses' => 'ScheduleController@update' ]);

        // Rubric Editing
	    Route::get('rubric',                         [ 'as' => 'rubric.index', 'uses' => 'VideoManagementController@rubric']);
	    Route::get('rubric/{competition_id}',        [ 'as' => 'rubric.view', 'uses' => 'VideoManagementController@rubric']);
	    Route::get('rubric/{competition_id}/{edit}', [ 'as' => 'rubric.edit', 'uses' => 'VideoManagementController@rubric']);

	    Route::post('rubric/{competition_id}/save',               [ 'as' => 'rubric.save', 'uses' => 'VideoManagementController@rubric_save']);
	    Route::get('rubric/{competition_id}/export',              [ 'as' => 'rubric.export', 'uses' => 'VideoManagementController@rubric_export']);
	    Route::get('rubric/{competition_id?}/copyto/{dest_id?}',  [ 'as' => 'rubric.copyto', 'uses' => 'VideoManagementController@rubric_copy_to']);

	    Route::get('ajax/rubric_view/{competition_id?}',   [ 'as' => 'ajax.rubric.view', 'uses' => 'VideoManagementController@rubric_view']);
	    Route::get('ajax/rubric_edit/{competition_id?}',   [ 'as' => 'ajax.rubric.edit', 'uses' => 'VideoManagementController@rubric_edit']);
	    Route::get('ajax/rubric_blank_row/{competition_id?}/{type?}/{id?}',   [ 'as' => 'ajax.rubric.blank_row', 'uses' => 'VideoManagementController@rubric_blank_row']);

	    // Nomination Management
	    Route::get('awards/{year}/{comp}/{div}', [ 'as' => 'awards.index', 'uses' => 'AwardsController@index']);

    });

    /* -----------------------------------------------------------------------------
    |                              Judge Routes                                     |
    ------------------------------------------------------------------------------*/

	/** @noinspection PhpParamsInspection */
    Route::middleware([ 'judgeFilter' ])->group( function () {
        // Delete individual score
        Route::get('team/{team_id}/delete_score/{score_run_id}', [
            'as' => 'display.teamscore.delete_score',
            'uses' => 'DisplayController@delete_score' ] )
            ->where('team_id', '\d+');
        Route::get('team/{team_id}/restore_score/{score_run_id}', [
            'as' => 'display.teamscore.restore_score',
            'uses' => 'DisplayController@restore_score' ] )
            ->where('team_id', '\d+');

        // Challenge Scoring
        Route::get('score', 									[ 'as' => 'score.choose_competition',	'uses' =>'ScoreController@index' ] );
        Route::get('score/c/{comp_id}', 						[ 'as' => 'score.choose_division',		'uses' =>'ScoreController@index' ] )
            ->where('competition_id', '\d+');
        Route::get('score/c/{comp_id}/d/{div_id}',				[ 'as' => 'score.choose_team',			'uses' =>'ScoreController@index' ] )
            ->where('competition_id', '\d+')
            ->where('division_id', '\d+');
        Route::get('score/c/{comp_id}/d/{div_id}/t/{team_id}',	[ 'as' => 'score.score_team',			'uses' =>'ScoreController@index' ] )
            ->where('comp_id', '\d+')
            ->where('div_id', '\d+')
            ->where('team_id', '\d+');
        Route::get('score/{team_id}/{challenge_id}', 			[ 'as' => 'score.doscore',	'uses' =>'ScoreController@doscore' ] )
            ->where('team_id', '\d+')
            ->where('challenge_id', '\d+');
        Route::post('score/save/{team_id}/{challenge_id}', 		[ 'as' => 'score.save',	'uses' =>'ScoreController@save' ] );

        // React Scoring
	    Route::get('scorer/{path?}', [ 'as' => 'scorer', 'uses' => 'ScoreController@scorer' ] )
	        ->where('path',".*");
	    Route::get('scorer/test/{challenge_id}', [ 'as' => 'challenges.test', 'uses' => 'ScoreController@scorer']);

        // Video Judging
        Route::get('video/judge', [
            'as' => 'video.judge.index',
            'uses' => 'ScoreVideosController@index' ]);
        Route::get('video/judge/{video_id}/edit', [
            'as' => 'video.judge.edit',
            'uses' => 'ScoreVideosController@edit' ]);
        Route::get('video/judge/{video_id}/show', [
            'as' => 'video.judge.show',
            'uses' => 'ScoreVideosController@show' ]);
        Route::get('video/judge/dispatch', [
            'as' => 'video.judge.dispatch',
            'uses' => 'ScoreVideosController@do_dispatch' ]);
        Route::get('video/judge/score/{video_id}', [
            'as' => 'video.judge.score',
            'uses' => 'ScoreVideosController@score' ]);
        Route::post('video/judge/store/{video_id}', [
            'as' => 'video.judge.store',
            'uses' => 'ScoreVideosController@store' ]);
        Route::post('video/judge/update/{video_id}', [
            'as' => 'video.judge.update',
            'uses' => 'ScoreVideosController@update' ]);
        Route::get('video/judge/clear/{video_id}/{judge_id}', [
            'as' => 'video.judge.clear_scores',
            'uses' => 'ScoreVideosController@clear_scores' ]);

	    /** @noinspection PhpParamsInspection */
        Route::prefix('/api/')->namespace('Api')->name('api.')->group(function() {
	        Route::get('challenges/{year}/{level}',   ['as' => 'challenges', 'uses' => 'ScoreApiController@challenges']);
	        Route::get('challenge/{challenge_id}',    ['as' => 'challenge', 'uses' => 'ScoreApiController@challenge']);
	        Route::post('scorer/save_scores',         ['as' => 'save_score', 'uses' => 'ScoreApiController@save_scores']);
	        Route::get('scorer/runs/{team_id}',       ['as' => 'team_runs', 'uses' => 'ScoreApiController@team_runs']);
	        Route::get('scorer/scores/{team_id}',     ['as' => 'team_scores', 'uses' => 'ScoreApiController@team_scores']);
	        Route::get('scorer/noms/{team_id}',       ['as' => 'team_scores', 'uses' => 'ScoreApiController@team_nominations']);
	        Route::post('scorer/save_noms/{team_id}', ['as' => 'team_scores', 'uses' => 'ScoreApiController@save_nominations']);
        });

    });

	/* -----------------------------------------------------------------------------
	|                            Teacher Routes                                     |
	------------------------------------------------------------------------------*/
	/** @noinspection PhpParamsInspection */
    Route::middleware([ 'teacherFilter' ])->group( function ()
    {
	    Route::resource('teacher/teams', 'TeacherTeamsController', [ 'names' => 'teacher.teams']);
	    Route::resource('teacher/videos', 'TeacherVideoController', [ 'names' => 'teacher.videos']);

        Route::get('teacher/video/{video_id}/delete/{file_id}', [
            'as' => 'uploader.delete_file',
            'uses' => 'UploadController@delete_file' ])
            ->where('video_id', '\d+')
            ->where('file_id', '\d+');

        Route::post('teacher/video/{video_id}/rename/{file_id}', [
            'as' => 'uploader.rename_file',
            'uses' => 'UploadController@rename_file' ])
            ->where('video_id', '\d+')
            ->where('file_id', '\d+');

        Route::get('uploader/{video_id}', [
            'as' => 'uploader.index',
            'uses' => 'UploadController@index' ])
            ->where('video_id', '\d+');
        Route::post('uploader/handler/{video_id}', [
            'as' => 'uploader.handler',
            'uses' => 'UploadController@handler' ])
            ->where('video_id', '\d+');;
        Route::get('uploader/progress', [
            'as' => 'uploader.progress',
            'uses' => 'UploadController@progress' ]);

        // New Combined Teacher Controller
        Route::get('teacher', [
            'as' => 'teacher.index',
            'uses' => 'TeacherController@index' ]);

        Route::post('teacher/save_tshirt', [ 'as' => 'teacher.save_tshirt', 'uses' => 'TeacherController@save_tshirt' ]);

        Route::get('validate_video/{video_id}', ['as' => 'validate_video', 'uses' => 'TeacherVideoController@validate_video']);

        Route::resource('students', 'StudentsController');
    });

	/* -----------------------------------------------------------------------------
	|                            Video Reviewer Routes                             |
	------------------------------------------------------------------------------*/
	/** @noinspection PhpParamsInspection */
	Route::middleware([ 'videoReviewerFilter' ])->group( function () {
		Route::get('video_review/{year?}/{any?}', ['as' => 'video_review', 'uses' => 'VideoReviewController@index'])
			->where('any',".*");

		/** @noinspection PhpParamsInspection */
		Route::prefix('/api/video_review/')->name('api.')->group(function() {
			Route::get('{year?}/review_status', ['as' => 'video_review.status', 'uses' => 'VideoReviewController@review_status']);
			Route::get('{year}/get_next', ['as' => 'video_review.get_next', 'uses' => 'VideoReviewController@get_next']);
			Route::get('{year}/fetch_video/{id}', ['as' => 'video_review.fetch_video', 'uses' => 'VideoReviewController@fetch_video']);
			Route::get('save_no_problems/{id}', [ 'uses' => 'VideoReviewController@save_no_problems']);
			Route::get('reviewed_videos/{year}/{id}', [ 'uses' => 'VideoReviewController@reviewed_videos']);
			Route::get('resolve_problem/{video_id}/{problem_id}', [ 'uses' => 'VideoReviewController@resolve_problem']);
			Route::post('save_problems/{id}', [ 'uses' => 'VideoReviewController@save_problems']);

			// Admin Specific features
			/** @noinspection PhpParamsInspection */
			Route::middleware(['adminFilter'])->group( function() {
				Route::get('/send_dq/{video_id}', ['uses' => 'VideoReviewController@send_dq' ]);
				Route::get('/set_review_status/{video}/{status}', ['uses' => 'VideoReviewController@set_review_status' ]);
				Route::get('/all_reviewed_videos/{year}', [ 'uses' => 'VideoReviewController@all_reviewed_videos' ]);
				Route::get('/dq_preview/{video_id}', function($video_id) {
					return new App\Mail\VideoDisqualification($video_id);
				});
			});
		});
	});

});
