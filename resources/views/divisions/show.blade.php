@extends('layouts.scaffold')

@section('script')
<script>
	var sorting_enabled = false;
	$(function () {
		$("#enable_sorting").bind('click', toggle_sortable);
	});

	function toggle_sortable(event) {
		event.preventDefault();
		if(sorting_enabled) {
				$("#challenge_list span").removeClass("ui-icon ui-icon-arrowthick-2-n-s");
				$("#challenge_list>tbody").sortable("destroy");
				$("#enable_sorting").text('Enable Sorting');
				sorting_enabled = false;
		} else {
			$("#challenge_list>tbody").sortable({
				opacity: 0.6,
				cursor: 'move',
				update: function() {
					$.post("{{ route('divisions.updateChallengeOrder', $division->id) }}",
						$("#challenge_list>tbody").sortable("serialize"),
						function(theResponse){
							$("#challenge_list>tbody").html(theResponse);
							$("#challenge_list span").addClass("ui-icon ui-icon-arrowthick-2-n-s");
						});
				}
			});
			$("#challenge_list span").addClass("ui-icon ui-icon-arrowthick-2-n-s");
			$("#enable_sorting").text('Disable Sorting');
			sorting_enabled = true;
		}
	}
</script>
@endsection

@section('main')
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Description</th>
			<th>Competition</th>
			<th>Level</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $division->name }}}</td>
			<td>{{{ $division->description }}}</td>
			<td>{{{ $division->competition->name }}}</td>
			<td>{{{ $division->level }}}</td>
            <td>{{ link_to_route('divisions.edit', 'Edit', array($division->id), array('class' => 'btn btn-info')) }}</td>
            <td>
                {!! Form::open(array('method' => 'DELETE', 'route' => array('divisions.destroy', $division->id)))  !!}
                    {!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
                {!! Form::close()  !!}
            </td>
		</tr>
	</tbody>
</table>

<h2 class="pull-left">Challenges</h2>

<div style="margin-top: 20px;" class="pull-right">
	{!! Form::open( [ 'route' => [ 'divisions.copyChallenges', $division->id ], 'class' => 'form-inline' ] )  !!}
	<div class="form-group">
		{!! Form::select('from_id', $division_list, null, [ 'class' => 'form-control' ] )  !!}
		{!! Form::submit('Copy', [ 'class' => 'btn btn-primary' ] )  !!}
		{{ link_to_route('divisions.clearChallenges', 'Clear', [ $division->id ], [ 'class' => 'btn btn-danger']) }}
	</div>
	{!! Form::close()  !!}
</div>

<table class="table table-striped table-bordered" id="challenge_list">
	<thead>
		<tr>
			<th>#</th>
			<th>Internal Name</th>
			<th>Display Name</th>
			<th>Rules</th>
			<th>SE</th>
			<th>Rand</th>
			<th colspan="2">Actions</th>
		</tr>
	</thead>

	<tbody>
		@include('divisions.partial.challenges', $challenges)
	</tbody>
</table>
{{ link_to_route('divisions.assign', 'Assign Challenges', array($division->id), array('class' => 'btn btn-primary')) }}
&nbsp;&nbsp;
{{ link_to('#', 'Enable Sorting', Array('class' => 'btn btn-info', 'id' => 'enable_sorting')) }}

@endsection
