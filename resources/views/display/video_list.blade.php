@extends('layouts.scaffold')

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
</script>
@endsection

@section('main')
<div class="col-md-8">
@if(count($videos))
    @if($winners)
        <a href="{{ route('display.video_list', [ 'comp_id' => $comp->id, 'winners' => '' ]) }}" class="btn btn-info btn-margin">Show All</a>
    @else
        <a href="{{ route('display.video_list', [ 'comp_id' => $comp->id, 'winners' => 'winners' ]) }}" class="btn btn-info btn-margin">Show Winners</a>
    @endif
	@foreach($videos as $div => $videos)
	<table class="table table-striped table-bordered">
		<thead>
			<tr class="div_row">
				<th class="header">{{ $div }}</th>
				<th class="header">School</th>
			</tr>
		</thead>
		<tbody>
			@foreach($videos as $name => $video)
				<tr class="score_row">
					<td>
					    @if(count($video->awards))
                            <img src="{{ asset('images/star.png') }}">
                        @endif
					    <a href="{{ route('display.show_video', [ $comp->id, $video->id ]) }}" >
							{{ $name }}
						</a>
						@if(count($video->awards))
						    <br>
					        @foreach($video->awards as $award)
                                Winner: {{ $award->name }}<br>
					        @endforeach
					    @endif
					</td>
					<td>{{ $video->school->name }}</td>
				</tr>
				@endforeach
		</tbody>
	</table>
	@endforeach
@else
	<h4>No Videos</h4>
@endif

</div>
@endsection