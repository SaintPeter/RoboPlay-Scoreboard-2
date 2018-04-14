@extends('layouts.scaffold')

@section('style')
<style>
.reported {
	width: 1000px;
}
.reported th {
	background-color: #428BCA;
	color: white;
	padding: 2px;
}

.reported th:first-child {
	text-align: left !important;
	width: 150px;
}

tr.score_row:nth-child(odd){
	background-color: #FAFAFA;
}

.reported td {
	border: 1px solid lightgrey;
	vertical-align: top;
}

.reported tbody td:first-child {
	width: 200px !important;
	text-align: left !important;
	padding-left: 12px;
}
</style>
@endsection


@section('main')
@include('partials.year_select')
@include('partials.scorenav', [ 'nav' => 'reported', 'year' => $year])
@inject('videoflag','App\Enums\VideoFlag')

{!! Form::open([ 'route' => 'video_scores.manage.process_report' ])  !!}
<table class="table-bordered reported">
	<thead>
		<th>Video</th>
		<th>Reported By</th>
		<th>Comment</th>
		<th>Response</th>
		<th>Action</th>
	</thead>
	<tbody>
		@if(count($comments_reported) > 0)
			@foreach($comments_reported as $comment)
			<tr class="score_row">
				<td>{{ link_to_route('video.judge.show', $comment->video->name, [ $comment->video->id ]) }}</td>
				<td>{{ $comment->user->name }}</td>
				<td>{{ $comment->comment }}</td>
				<td>{!! Form::textarea('resolution', $comment->resolution, [ 'cols' => 40, 'rows' => 4 ])  !!}</td>
				<td class="text-center">
					{!! Form::button('Absolve', [ 'type' => 'submit', 'name'=> 'absolve', 'value' => $comment->id ,'class' => 'btn btn-success' ])  !!}
					{!! Form::button('Disqualify', [ 'type' => 'submit', 'name'=> 'dq',  'value' => $comment->id ,'class' => 'btn btn-danger' ])  !!}</td>
			</tr>
			@endforeach
		@else
			<tr><td colspan="5" class="text-center">No Reported Videos</td></tr>
		@endif
	</tbody>
</table>
<br />
<table class="table-bordered reported">
	<thead>
		<th>Video</th>
		<th>Reported By</th>
		<th>Comment</th>
		<th>Response</th>
		<th>Status</th>
	</thead>
	<tbody>
		@if(count($comments_resolved) > 0)
			@foreach($comments_resolved as $comment)
			<tr class="score_row">
				<td>{{ link_to_route('video.judge.show', $comment->video->name, [ $comment->video->id ]) }}</td>
				<td>{{ $comment->user->name }}</td>
				<td>{{ $comment->comment }}</td>
				<td>{{ $comment->resolution }}</td>
				<td class="text-center">
					@if($comment->video->flag == $videoflag::Normal)
						Absolved
						<a href="{{ route('video_scores.manage.unresolve', [ $comment->id ]) }}" class="pull-right">
							<span class="glyphicon glyphicon-refresh" title="Unresolve"></span>
						</a>
					@else
						Disqualified
						<a href="{{ route('video_scores.manage.unresolve', [ $comment->id ]) }}" class="pull-right">
							<span class="glyphicon glyphicon-refresh" title="Unresolve"></span>
						</a>
					@endif
				</td>
			</tr>
			@endforeach
		@else
			<tr><td colspan="5" class="text-center">No Resolved Videos</td></tr>
		@endif
	</tbody>
</table>

{!! Form::close()  !!}
@endsection