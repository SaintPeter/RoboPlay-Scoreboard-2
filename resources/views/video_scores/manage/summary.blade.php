@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.tablesorter.min.js') }}
	{{ HTML::style('css/tablesorter.css') }}
@endsection

@section('style')
<style>
.div_row th {
	background-color: #428BCA !important;
	color: white;
}

.div_row th:first-child {
	font-weight: bold;
}

.score_row td {
	text-align: center;
}

.score_row td:first-child {
	text-align: left;
	padding-left: 15px;
	width: 60%;
}

</style>
@endsection

@section('script')
<script>
	var myTextExtraction = function(node)
	{
	    // extract data from markup and return it
	    return (node.innerHTML=='-') ? -1 : node.innerHTML ;
	}
	$(function() {
		$( '.table' ).tablesorter({textExtraction: myTextExtraction});

	});
</script>
@endsection

@section('main')
@include('partials.year_select')
@include('partials.scorenav', [ 'nav' => 'summary', 'year' => $year])
<div class="col-md-8">
	@foreach($output as $comp => $divs)
		@foreach($divs as $div => $videos)
		<table class="table table-striped table-bordered tablesorter">
			<thead>
				<tr class="div_row">
					<th class="header">{{ $comp }} - {{ $div }}</th>
					<th class="header">General</th>
					<th class="header">Part</th>
					<th class="header">Compute</th>
				</tr>
			</thead>
			<tbody>
				@foreach($videos as $video)
				<tr class="score_row">
					<td>{{ $video->name }}
						<a href="{{ route('video.judge.show', [ $video->id ]) }}" class="pull-right">
							<span class="glyphicon glyphicon-eye-open" title="Watch"></span>
						</a>
					</td>
					<td>{{ $video->general_scores_count() }}</td>
					<td>{{ $video->part_scores_count() }}</td>
					<td>{{ $video->compute_scores_count() }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
		@endforeach
	@endforeach
</div>
@endsection