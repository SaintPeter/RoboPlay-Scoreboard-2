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
				"Delete Competition": function() {
					$( this ).dialog( "close" );
					$(delete_button).parent().submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
</script>
@endsection

@section('main')
<p>{{ link_to_route('competitions.create', 'Add Competition', [], ['class' => 'btn btn-primary']) }}
	<span class="pull-right">
		{{ link_to_route('competitions.freeze.all', 'Freeze All', [], ['class' => 'btn btn-info btn-margin']) }}
		{{ link_to_route('competitions.unfreeze.all', 'Unfreeze All', [], ['class' => 'btn btn-warning btn-margin']) }}
	</span>
	</p>


@if ($competitions->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Location</th>
				<th>Address</th>
				<th>Color</th>
				<th>Event Date</th>
				<th>Freeze</th>
				<th>Active</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($competitions as $competition)
				<tr>
					<td>{{{ $competition->name }}}</td>
					<td>{{{ $competition->description }}}</td>
					<td>{{{ $competition->location }}}</td>
					<td>{{ nl2br($competition->address) }}</td>
					<td><div style="background-color: {{ $competition->color }};width: 20px; height: 20px;"></div></div></td>
					<td>{{{ $competition->event_date }}}</td>
					<td>
						{{{ $competition->freeze_time }}}<br />
						@if($competition->frozen)
							{{ link_to_route('competition.toggle_frozen', "Frozen", [ $competition->id ], ['class' => 'btn btn-xs btn-info', 'title' => 'Click to Unfreeze' ] ) }}
						@else
							{{ link_to_route('competition.toggle_frozen', "Unfrozen", [ $competition->id ], ['class' => 'btn btn-xs btn-warning', 'title' => 'Click to Freeze' ] ) }}
						@endif
					</td>
					<td style="vertical-align: middle;">
						@if($competition->active)
							{{ link_to_route('competition.toggle_active', "Active", [ $competition->id ], ['class' => 'btn btn-xs btn-success', 'title' => 'Click to Deactivate' ] ) }}
						@else
							{{ link_to_route('competition.toggle_active', "Inactive", [ $competition->id ], ['class' => 'btn btn-xs btn-info', 'title' => 'Click to Activate' ] ) }}
						@endif
					</td>
                    <td>{{ link_to_route('competitions.edit', 'Edit', array($competition->id), array('class' => 'btn btn-info btn-margin')) }}
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('competitions.destroy', $competition->id), 'style' => 'display: inline-block'))  !!}
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger btn-margin'))  !!}
                        {!! Form::close()  !!}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no competitions
@endif


<div id="dialog-confirm" title="Delete Competition?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This competition, all divisions, all teams, and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@endsection
