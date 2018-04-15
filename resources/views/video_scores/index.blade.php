@extends('layouts.scaffold')

@section('head')
	{{ HTML::style ('https://gitcdn.github.io/bootstrap-toggle/2.2.0/css/bootstrap-toggle.min.css') }}
	{{ HTML::script('https://gitcdn.github.io/bootstrap-toggle/2.2.0/js/bootstrap-toggle.min.js') }}
@endsection

@section('style')
<style>
.comment {
	background-image: url(/css/images/comment_mark.png);
	background-position: top right;
	background-repeat: no-repeat;
}
.holder {
	border: 1px solid gray;
	border-radius: 4px;
	padding: 0px;
	background-color: rgb(245, 245, 245);
	height: 260px;
	margin: 10px 0 10px 0;
}

.inner {
	padding: 12px;
}

.header {
	text-align: center;
	font-weight: bold;
	border-radius: 4px 4px 0px 0px;
	width: 100%;
	margin: 0px;
	padding: 6px 12px;
	color: white;
}

.button_box {
	position: absolute;
	top: 200px;
	width: 85%;
}

.general {
	background-color: #428BCA;
}

.part {
	background-color: #5BC0DE;
}

.compute {
	background-color: #5CB85C
}
.scored_container {
	width: 800px;
	margin-left: auto;
	margin-right: auto;
	margin-top: 15px;
	clear:both;
}
.scored_table {
	width: 100%;
}
.comp_row td:first-child {
	font-size: 1.1em;
	padding: 3px;
	text-align: left !important;
}
.comp_row td {
	background-color: #428BCA;
	color: white;
	text-align: center;
}

.score_row td:first-child {
	padding-left: 20px;
	width: 250px;
	text-align: left !important;
}
.score_row td {
	text-align: center;
	width: 60px;
	text-align: center;
	border: 1px solid lightgray;
}

td.score:nth-child(odd) {
	background-color: rgb(245, 245, 245);
}
</style>
@endsection

@section('script')
<script>
$.widget.bridge('uitooltip', $.ui.tooltip);
$(document).uitooltip({
          content: function () {
              return $(this).prop('title');
          }
      });
</script>
@endsection

@inject('videotype', 'App\Enums\VideoType')
@inject('videoflag', 'App\Enums\VideoFlag') 

@section('main')
{!! Form::open([ 'route' => 'video.judge.dispatch', 'method' => 'get' ])  !!}
@if(count($comp_list))
	<h4>Open Video Competitions</h4>
	@foreach($comp_list as $comp => $divs)
		<p><strong>{{$comp}}:</strong> {{ join(', ', $divs) }} </p>
	@endforeach
@else
	<h4 style="color: red;">No Open Video Competitions</h4>
@endif
	<div class="col-sm-6 col-md-4">
		<div class="holder">
			<div class="header general">General Videos</div>
			<div class="inner">
				<p>General vidoes will be scored on:
					<ul>
						<li>Storyline</li>
						<li>Choreography</li>
						<li>Interesting Task</li>
					</ul>
					<br />
				All judges may judge these videos.</p>
				<div class="text-center button_box">
					@if($scored_count[$videotype::General] >= $total_count[$videotype::General] AND $total_count[$videotype::General] > 0)
						<button class="btn btn-primary btn-margin disabled">Score Videos</button>
					@else
						<button class="btn btn-primary btn-margin">Score Videos</button>
					@endif
					<p>Scored: {{ $scored_count[$videotype::General] }} of {{ $total_count[$videotype::General] }}</p>
				</div>
			</div>
		</div>
	</div>

<div class="col-sm-6 col-md-4">
	<div class="holder">
		<div class="header part">Custom Part Videos</div>
		<div class="inner">
			<p>These videos contain a custom designed part and will be scored on the design and use of that part.<br /><br />
			Judges should have a background in mechanical design or robotics.</p>
			<div class="text-center button_box">
					@if($scored_count[$videotype::Custom] >= $total_count[$videotype::Custom] AND $total_count[$videotype::Custom] > 0)
						<input id="judge_custom" name="judge_custom" disabled class="disabled" type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Will Judge" data-off="Won't Judge" {{ $judge_custom }}>
					@else
						<input id="judge_custom" name="judge_custom" type="checkbox" data-toggle="toggle" data-onstyle="info" data-on="Will Judge" data-off="Won't Judge" {{ $judge_custom }}>
					@endif
				<p>Scored: {{ $scored_count[$videotype::Custom] }} of {{ $total_count[$videotype::Custom] }}</p>
			</div>
		</div>
	</div>
</div>

<div class="col-sm-6 col-md-4">
	<div class="holder">
		<div class="header compute">Computational Thinking Videos</div>
		<div class="inner">
			<p>These videos will be judged primarily on the content of the source code written to produce them.<br /><br />
			   Judges should have a background in reading source code.</p>
			<div class="text-center button_box">
					@if($scored_count[$videotype::Compute] >= $total_count[$videotype::Compute] AND $total_count[$videotype::Compute] > 0)
						<input id="judge_compute" name="judge_compute" disabled class="disabled" type="checkbox" data-toggle="toggle" data-onstyle="success" data-on="Will Judge" data-off="Won't Judge" {{ $judge_compute }}>
					@else
						<input id="judge_compute" name="judge_compute" type="checkbox" data-toggle="toggle" data-onstyle="success" data-on="Will Judge" data-off="Won't Judge" {{ $judge_compute }}>
					@endif
				<p>Scored: {{ $scored_count[$videotype::Compute] }} of {{ $total_count[$videotype::Compute] }}</p>
			</div>
		</div>
	</div>
</div>
{!! Form::close()  !!}

<div class="text-center"><br /><strong>Note:</strong> Each video should be scored on its own merits as compared to the rubric, rather than in comparison to other videos.</div>

<div class="scored_container">
	<h3>Previously Scored Videos</h3>
	<table class="scored_table">
		<thead>
		</thead>
		<tbody>
			@if(count($videos))
				@foreach($videos as $comp => $video_list)
					<tr class="comp_row">
						<td>{{ $comp }}</td>
						@foreach($types as $type)
							<td class="type">{{ ucwords($type) }}</td>
						@endforeach
						<td>Action</td>
					</tr>
					@foreach($video_list as $vid_title => $scores)
						<tr class="score_row">
							 @if($scores['flag'] == $videoflag::Normal)
							    <td class="{{ $scores['comments'] ? 'comment' : '' }}" title="{{ $scores['comments'] }}">
									<a href="{{ route('video.judge.edit', [ $scores['video_id'] ]) }}">
										<span class="glyphicon glyphicon-edit"></span>
										<strong>{{ $vid_title }}</strong>
									</a>
								</td>
							@elseif($scores['flag'] == $videoflag::Review)
								<td class="comment text-warning" title="<strong>Video Under Review</strong><br>{{ $scores['comments'] }}">
									<span class="glyphicon glyphicon-exclamation-sign"></span>
									<strong>{{ $vid_title }}</strong>
								</td>
							@else
								<td class="comment text-danger" title="<strong>Video Disqualified</strong><br>{{ $scores['comments'] }}">
									<span class="glyphicon glyphicon-remove"></span>
									<strong>{{ $vid_title }}</strong>
								</td>
							@endif
								@foreach($types as $index => $type)
									@if($scores[$index] == '-')
										<td class="score">-</td>
									@else
										<td class="score">{{ $scores[$index]->total }}</td>
									@endif
								@endforeach
							<td>
								<a href="{{ route('video.judge.clear_scores', [ $scores['video_id'], Auth::user()->id ]) }}" class="btn btn-xs btn-danger">
									<span class="glyphicon glyphicon-remove"></span>
								</a>
							</td>
						</tr>
					@endforeach
				@endforeach
			@else
				<tr><td>No Videos Scored</td></tr>
			@endif
		</tbody>
	</table>
</div>



@endsection