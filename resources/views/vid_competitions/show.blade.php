@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('vid_competitions.index', 'Return to List', [], ['class' => 'btn btn-primary'] ) }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Event Start</th>
			<th>Event End</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $vid_competition->name }}}</td>
					<td>{{{ $vid_competition->event_start }}}</td>
					<td>{{{ $vid_competition->event_end }}}</td>
                    <td>{{ link_to_route('vid_competitions.edit', 'Edit', array($vid_competition->id), array('class' => 'btn btn-info btn-margin')) }}
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('vid_competitions.destroy', $vid_competition->id), 'style' => 'display: inline-block'))  !!}
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger btn-margin'))  !!}
                        {!! Form::close()  !!}
                    </td>
		</tr>
	</tbody>
</table>

@endsection
