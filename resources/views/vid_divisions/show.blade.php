@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('vid_divisions.index', 'Return to all vid_divisions', ['class' => 'btn btn-primary']) }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
				<th>Description</th>
				<th>Display Order</th>
				<th>Competition</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $vid_division->name }}}</td>
					<td>{{{ $vid_division->description }}}</td>
					<td>{{{ $vid_division->display_order }}}</td>
					<td>{{{ $vid_division->competition_id }}}</td>
                    <td>{{ link_to_route('vid_divisions.edit', 'Edit', array($vid_division->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('vid_divisions.destroy', $vid_division->id)))  !!}
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
                        {!! Form::close()  !!}
                    </td>
		</tr>
	</tbody>
</table>

@endsection
