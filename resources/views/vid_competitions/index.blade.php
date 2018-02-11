@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('vid_competitions.create', 'Add Video Competition',[], ['class' => 'btn btn-primary']) }}</p>


<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Start Date</th>
			<th>End Date</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($vid_competitions->count())
			@foreach ($vid_competitions as $vid_competition)
				<tr>
					<td>{{{ $vid_competition->name }}}</td>
					<td>{{{ $vid_competition->event_start->toFormattedDateString() }}}</td>
					<td>{{{ $vid_competition->event_end->toFormattedDateString() }}}</td>
	                <td>{{ link_to_route('vid_competitions.edit', 'Edit', array($vid_competition->id), array('class' => 'btn btn-info btn-margin')) }}

	                    {!! Form::open(array('method' => 'DELETE', 'route' => array('vid_competitions.destroy', $vid_competition->id), 'style' => 'display: inline-block'))  !!}
	                        {!! Form::submit('Delete', array('class' => 'btn btn-danger btn-margin'))  !!}
	                    {!! Form::close()  !!}
	                </td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="4">There are no Video Competitions</td></tr>
		@endif
	</tbody>
</table>

@endsection
