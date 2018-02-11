<div style="margin: 10px 0px;">
    <?php $year = ($year) ? $year : ''; ?>
	<ul class="nav nav-pills">
		<li @if($nav == 'by_judge') class="active" @endif>{{ link_to_route('video_scores.manage.index', 'By Judge', [ $year ]) }}</li>
		<li @if($nav == 'by_video') class="active" @endif>{{ link_to_route('video_scores.manage.by_video', 'Scores By Video', [ $year ]) }}</li>
		<li @if($nav == 'reported') class="active" @endif>{{ link_to_route('video_scores.manage.reported', 'Reported Videos', [ $year ]) }}</li>
		<li @if($nav == 'summary') class="active" @endif>{{ link_to_route('video_scores.manage.summary', 'Video Perf.', [ $year ]) }}</li>
		<li @if($nav == 'judges') class="active" @endif>{{ link_to_route('video_scores.manage.judge_performance', 'Judge Perf.', [ $year ]) }}</li>
		<li @if($nav == 'graphs') class="active" @endif>{{ link_to_route('video_scores.manage.graphs', 'Graphs', [ $year ]) }}</li>
	</ul>
</div>