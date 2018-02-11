@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('teams.index', 'Return to all teams', null, [ 'class' => 'btn btn-primary' ]) }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Division</th>
			<th>County/District/School</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $team->name }}}</td>
			<td>{{ join('<br />', $team->student_list()) }}</td>
			<td>{{{ $team->division->longname() }}}</td>
			<td>
				@if(isset($team->school))
					<strong>C:</strong> {{ $team->school->county }}<br />
					<strong>D:</strong> {{ $team->school->district }}<br />
					<strong>S:</strong> {{ $team->school->name }}
				@else
					Not Set
				@endif
			</td>
    	    <td>{{ link_to_route('teams.edit', 'Edit', array($team->id), array('class' => 'btn btn-info')) }}</td>
		</tr>
	</tbody>
</table>

@endsection
