@extends('layouts.scaffold')

@section('main')
<p>{{ link_to_route('vid_divisions.create', 'Add New Video Division', null, [ 'class' => 'btn btn-primary' ]) }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Description</th>
			<th>Display Order</th>
			<th>Competition</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($vid_divisions->count())
			@foreach ($vid_divisions as $vid_division)
				<tr>
					<td>{{{ $vid_division->name }}}</td>
					<td>{{{ $vid_division->description }}}</td>
					<td>{{{ $vid_division->display_order }}}</td>
					<td>{{{ $vid_division->competition->name }}}</td>
                    <td>{{ link_to_route('vid_divisions.edit', 'Edit', array($vid_division->id), array('class' => 'btn btn-info')) }}
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('vid_divisions.destroy', $vid_division->id), 'style' => 'display:inline;'))  !!}
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
                        {!! Form::close()  !!}
                    </td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="5"  class="text-center">No Video Divisions</td></tr>
		@endif
	</tbody>
</table>
@endsection
