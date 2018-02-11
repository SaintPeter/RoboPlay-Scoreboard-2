@extends('layouts.scaffold')

@section('script')
<script>
$(function() {
		$(".confirm").click(function(){
			if (!confirm("Are you sure you want to clear scores?")){
				return false;
			}
		});
	});
</script>
@endsection

@section('main')
<p>
	{{ link_to_route('divisions.create', 'Add New Division',array(), array('class' => 'btn btn-primary btn-margin')) }}
	<span class="pull-right">{{link_to_route('divisions.clear_all_scores', 'Clear All Division Scores', null, ['class' => 'btn btn-warning btn-margin confirm']) }}</span>

	</p>

@if ($divisions->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Order</th>
				<th>Level</th>
				<th>Competition</th>
				<th>Challenges</th>
				<th colspan="4">Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($divisions as $division)
				<tr>
					<td>{{{ $division->name }}}</td>
					<td>{{{ $division->description }}}</td>
					<td>{{{ $division->display_order }}}</td>
					<td>{{{ $division->level }}}</td>
					<td>{{{ $division->competition->name }}}</td>
					<td>{{{ $division->challenges->count() }}}</td>
					<td>{{ link_to_route('divisions.show', 'Show', array($division->id), array('class' => 'btn btn-default')) }}</td>
                    <td>{{ link_to_route('divisions.edit', 'Edit', array($division->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('divisions.destroy', $division->id)))  !!}
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
                        {!! Form::close()  !!}
                    </td>
                    <td>{{link_to_route('divisions.clear_scores', 'Clear Scores', [ $division->id ], ['class' => 'btn btn-warning btn-margin confirm']) }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no divisions
@endif

@endsection
