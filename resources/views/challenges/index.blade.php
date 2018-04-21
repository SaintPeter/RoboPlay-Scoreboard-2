@extends('layouts.scaffold')

@section('script')
<script>
	var delete_button = '';
	$(function() {
		$("[value|='Delete']").click(function(e) {
			e.preventDefault();
			delete_button = this;
			$("#dialog-confirm").dialog('open');
		});

		$( "#dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:180,
			width:550,
			modal: true,
			buttons: {
				"Delete Challenge": function() {
					$( this ).dialog( "close" );
					$(delete_button).parent().submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		// Level Selector
		$('#level_select').change(function (e) {
			window.location = "{{ route('challenges.index') }}" +  "?level_select=" + $(this).val();
		});
	});
</script>
@endsection

@section('main')
@include('partials.year_select')
@inject("Challenge",'App\Models\Challenge')
<div class="pull-right">
	{!! Form::select('level_select', $Challenge::$levels, Session::get('level_select', 0), [ 'class' => 'form-control', 'id' => 'level_select' ])  !!}
</div>

<p>{{ link_to_route('challenges.create', 'Add Challenge', [], [ 'class' => 'btn btn-primary' ]) }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Internal Name</th>
			<th>Display Name</th>
			<th  style="width:568px">Rules</th>
			<th>Points</th>
			<th>SE</th>
			<th>Level</th>
			<th>Year</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($challenges->count())
			@foreach ($challenges as $challenge)
				<tr>
					<td>{{{ $challenge->internal_name }}}</td>
					<td>{{{ $challenge->display_name }}}</td>
					<td>{!! nl2br($challenge->rules) !!}</td>
					<td>{{{ $challenge->points }}}</td>
					<td>{{{ $challenge->score_elements->count() }}}</td>
					<td>{{{ $challenge->level }}}</td>
					<td>{{{ $challenge->year }}}</td>
                    <td style="whitespace: nobreak;">{{ link_to_route('challenges.show', 'Show', array($challenge->id), array('class' => 'btn btn-default btn-margin')) }}
                    	{{ link_to_route('challenges.edit', 'Edit', array($challenge->id), array('class' => 'btn btn-info btn-margin')) }}
                    	{{ link_to_route('challenges.duplicate', 'Copy', array($challenge->id), array('class' => 'btn btn-success btn-margin')) }}
	                    {!! Form::open(array('method' => 'DELETE', 'route' => array('challenges.destroy', $challenge->id),'style' => 'display: inline-block'))  !!}
	                        {!! Form::submit('Delete', array('class' => 'btn btn-danger btn-margin'))  !!}
	                    {!! Form::close()  !!}
                    </td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="8">There are no Challenges</td></tr>
		@endif
	</tbody>
</table>
<div id="dialog-confirm" title="Delete Challenge?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This challenge and all score elements will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
@endsection
