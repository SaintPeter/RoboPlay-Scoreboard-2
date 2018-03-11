<?php

use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator;

try {
// Home
	Breadcrumbs::register('home', function (BreadcrumbsGenerator $bc) {
		$bc->push('Home', route('home'));
	});


	Breadcrumbs::register('display.video_list', function (BreadcrumbsGenerator $bc, $competition) {
		$bc->parent('home');
		$bc->push('Video List', route('display.video_list', $competition));
	});

// ChallengesController.php  (4 usages found)
	Breadcrumbs::register('challenges.index', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Manage Challenges', route('challenges.index'));
	});
	Breadcrumbs::register('challenges.create', function (BreadcrumbsGenerator $bc) {
		$bc->parent('challenges.index');
		$bc->push('Add Challenge');
	});
	Breadcrumbs::register('challenges.show', function (BreadcrumbsGenerator $bc) {
		$bc->parent('challenges.index');
		$bc->push('Show Challenge');
	});
	Breadcrumbs::register('challenges.edit', function (BreadcrumbsGenerator $bc) {
		$bc->parent('challenges.index');
		$bc->push('Edit Challenge');
	});

// DivisionsController.php  (6 usages found)
	Breadcrumbs::register('divisions.index', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Competition Divisions', route('divisions.index'));
	});
	Breadcrumbs::register('divisions.create', function (BreadcrumbsGenerator $bc) {
		$bc->parent('divisions.index');
		$bc->push('Add Division', route('divisions.create'));
	});
	Breadcrumbs::register('divisions.show', function (BreadcrumbsGenerator $bc, $div_id) {
		$bc->parent('divisions.index');
		$bc->push('Show Division', route('divisions.show', [$div_id]));
	});

	Breadcrumbs::register('divisions.assign', function (BreadcrumbsGenerator $bc, $div_id) {
		$bc->parent('divisions.show', $div_id);
		$bc->push('Assign Challenges', route('divisions.assign', [$div_id]));
	});

	Breadcrumbs::register('divisions.edit', function (BreadcrumbsGenerator $bc, $div_id) {
		$bc->parent('divisions.index');
		$bc->push('Edit Division', route('divisions.edit', [ $div_id ]));
	});

// Competitions
	Breadcrumbs::register('competitions.index', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Manage Competitions', route('competitions.index'));
	});

	Breadcrumbs::register('competitions.edit', function (BreadcrumbsGenerator $bc) {
		$bc->parent('competitions.index');
		$bc->push('Edit Competition');
	});

	Breadcrumbs::register('competitions.create', function (BreadcrumbsGenerator $bc) {
		$bc->parent('competitions.index');
		$bc->push('Add Competition', route('competitions.create'));
	});

// CompYears
	Breadcrumbs::register('compyears.index', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Manage Competition Years', route('compyears.index'));
	});

	Breadcrumbs::register('compyears.edit', function (BreadcrumbsGenerator $bc) {
		$bc->parent('compyears.index');
		$bc->push('Edit Competition Year');
	});

	Breadcrumbs::register('compyears.create', function (BreadcrumbsGenerator $bc) {
		$bc->parent('compyears.index');
		$bc->push('Create New Competition Year', route('compyears.create'));
	});

	// Admin Stuff
	Breadcrumbs::register('list_users', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('List Users', route('list_users'));
	});

	Breadcrumbs::register('invoice_review', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Invoice Review', route('invoice_review'));
	});

	Breadcrumbs::register('data_export', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Data Export', route('data_export'));
	});
	

// Display Controller
	Breadcrumbs::register('display.teamscore', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Team Score');
	});

// Video Judging
	Breadcrumbs::register('video.judge.index', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Judge Videos', route('video.judge.index'));
	});

	Breadcrumbs::register('video.judge.score', function (BreadcrumbsGenerator $bc, $video_id) {
		$bc->parent('video.judge.index');
		$bc->push('Score Video', route('video.judge.score', [$video_id]));
	});

	Breadcrumbs::register('video.judge.edit', function (BreadcrumbsGenerator $bc, $video_id) {
		$bc->parent('video.judge.index');
		$bc->push('Edit Video Score', route('video.judge.edit', [$video_id]));
	});

// Video Competitions
	Breadcrumbs::register('vid_competitions.index', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Video Competitions', route('vid_competitions.index'));
	});

	Breadcrumbs::register('vid_competitions.edit', function (BreadcrumbsGenerator $bc, $vid_comp_id) {
		$bc->parent('vid_competitions.index');
		$bc->push('Edit Video Competition', route('vid_competitions.edit', [$vid_comp_id]));
	});

	Breadcrumbs::register('vid_competitions.create', function (BreadcrumbsGenerator $bc) {
		$bc->parent('vid_competitions.index');
		$bc->push('Add Video Competition', route('vid_competitions.create'));
	});

	// Teacher Teams
	Breadcrumbs::register('teacher.index', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Manage Teams and Videos', route('teacher.index'));
	});

	Breadcrumbs::register('teacher.teams.create', function (BreadcrumbsGenerator $bc) {
		$bc->parent('teacher.index');
		$bc->push('Create Team', route('teacher.teams.create'));
	});

	Breadcrumbs::register('teacher.teams.edit', function (BreadcrumbsGenerator $bc, $team_id) {
		$bc->parent('teacher.index');
		$bc->push('Edit Team', route('teacher.teams.edit', [ $team_id ]));
	});


	// Teacher Videos
	Breadcrumbs::register('teacher.videos.show', function (BreadcrumbsGenerator $bc, $video_id) {
		$bc->parent('teacher.index');
		$bc->push('Video Preview', route('teacher.videos.show', [$video_id]));
	});

	Breadcrumbs::register('teacher.videos.create', function (BreadcrumbsGenerator $bc) {
		$bc->parent('teacher.index');
		$bc->push('Create Video', route('teacher.videos.create'));
	});

	Breadcrumbs::register('teacher.videos.edit', function (BreadcrumbsGenerator $bc, $video_id) {
		$bc->parent('teacher.index');
		$bc->push('Video Preview', route('teacher.videos.show', [$video_id]));
		$bc->push('Edit Video', route('teacher.videos.edit', [$video_id]));
	});
	
// Video Scores
	Breadcrumbs::register('video_scores.manage.index', function (BreadcrumbsGenerator $bc, $year = null) {
		$bc->parent('home');
		$bc->push('Manage Video Scores', route('video_scores.manage.index', [ $year ]));
		$bc->push('By Judge', route('video_scores.manage.index'));
	});

	Breadcrumbs::register('video_scores.manage.by_video', function (BreadcrumbsGenerator $bc, $year = null) {
		$bc->parent('home');
		$bc->push('Manage Video Scores', route('video_scores.manage.index', [$year]));
		$bc->push('Scores by Video', route('video_scores.manage.by_video'));
	});

	Breadcrumbs::register('video_scores.manage.reported', function (BreadcrumbsGenerator $bc, $year = null) {
		$bc->parent('home');
		$bc->push('Manage Video Scores', route('video_scores.manage.index', [$year]));
		$bc->push('Reported Videos', route('video_scores.manage.reported', [$year]));
	});

	Breadcrumbs::register('video_scores.manage.summary', function (BreadcrumbsGenerator $bc, $year = null) {
		$bc->parent('home');
		$bc->push('Manage Video Scores', route('video_scores.manage.index', [$year]));
		$bc->push('Scoring Summary', route('video_scores.manage.summary', [$year]));
	});

	Breadcrumbs::register('video_scores.manage.judge_performance', function (BreadcrumbsGenerator $bc, $year = null) {
		$bc->parent('home');
		$bc->push('Manage Video Scores', route('video_scores.manage.index', [$year]));
		$bc->push('Judge Performance', route('video_scores.manage.judge_performance', [$year]));
	});

	Breadcrumbs::register('video_scores.manage.graphs', function (BreadcrumbsGenerator $bc, $year = null) {
		$bc->parent('home');
		$bc->push('Manage Video Scores', route('video_scores.manage.index', [$year]));
		$bc->push('Graphs', route('video_scores.manage.graphs', [$year]));
	});
	
// Team Management
	Breadcrumbs::register('teams.index', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Manage Teams', route('teams.index'));
	});

	Breadcrumbs::register('teams.edit', function (BreadcrumbsGenerator $bc, $team_id) {
		$bc->parent('teams.index');
		$bc->push('Edit Team', route('teams.edit', [$team_id]));
	});

	Breadcrumbs::register('teams.create', function (BreadcrumbsGenerator $bc) {
		$bc->parent('teams.index');
		$bc->push('Add Team', route('teams.create'));
	});

	Breadcrumbs::register('teams.show', function (BreadcrumbsGenerator $bc, $team_id) {
		$bc->parent('teams.index');
		$bc->push('Show Team', route('teams.show', [$team_id]));
	});
	
	// Video Management
	Breadcrumbs::register('videos.index', function (BreadcrumbsGenerator $bc) {
		$bc->parent('home');
		$bc->push('Manage Videos', route('videos.index'));
	});

	Breadcrumbs::register('videos.create', function (BreadcrumbsGenerator $bc) {
		$bc->parent('videos.index');
		$bc->push('Create Video', route('videos.create'));
	});

	Breadcrumbs::register('videos.edit', function (BreadcrumbsGenerator $bc, $video_id) {
		$bc->parent('videos.index');
		$bc->push('Edit Video', route('videos.edit', [$video_id]));
	});

	Breadcrumbs::register('videos.show', function (BreadcrumbsGenerator $bc, $video_id) {
		$bc->parent('videos.index');
		$bc->push('View Video', route('videos.show', [$video_id]));
	});

	// Uploader
	Breadcrumbs::register('uploader.index', function (BreadcrumbsGenerator $bc, $video_id) {
		$bc->parent('teacher.index');
		$bc->push('View Video', route('teacher.videos.show', [$video_id]));
		$bc->push('Upload Video Files', route('uploader.index', [$video_id]));
	});

	Breadcrumbs::register('videos.uploader', function (BreadcrumbsGenerator $bc, $video_id) {
		$bc->parent('videos.index');
		$bc->push('View Video', route('videos.show', [$video_id]));
		$bc->push('Video Uploader', route('videos.uploader', [$video_id]));
	});

} catch (\DaveJamesMiller\Breadcrumbs\Facades\DuplicateBreadcrumbException $e) {
	echo "Duplicate Breadcrumb Route Exception " . $e->getMessage();
}