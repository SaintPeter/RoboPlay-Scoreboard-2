@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('competitions.index', 'Return To Competitions', [], [ 'class' => 'btn btn-primary' ]) }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
				<th>Description</th>
				<th>Location</th>
				<th>Address</th>
				<th>Event_date</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $competition->name }}}</td>
					<td>{{{ $competition->description }}}</td>
					<td>{{{ $competition->location }}}</td>
					<td>{{{ $competition->address }}}</td>
					<td>{{{ $competition->event_date }}}</td>
                    <td>{{ link_to_route('competitions.edit', 'Edit', array($competition->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('competitions.destroy', $competition->id)))  !!}
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
                        {!! Form::close()  !!}
                    </td>
		</tr>
	</tbody>
</table>

@endsection
