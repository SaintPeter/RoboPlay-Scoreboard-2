@extends('layouts.scaffold')

@section('style')
<style>
.Paid, .confirmed {
	background-color: lightgreen !important;
}

.Unpaid, .unconfirmed {
	background-color: pink !important;
}

.ui-widget-overlay {
    background: url('images/ui-bg_flat_0_aaaaaa_40x100.png') repeat-x scroll 100% 100% #AAA;
    opacity: 0.3;
}

.summary_table {
	width: 500;
	background-color: white;
}

.narrow {
	width: 350px;
	white-space:nowrap;
}

.clear { clear: both; }

.validation_note {
    padding-left: 5em;
    font-style: italic;
    display: block;
    color: blueviolet;
}
.validation_status {
    vertical-align: top;
}

.validation_table td {
    border-top: 1px solid darkgray;
}

.validation_table th {
    border-bottom: 2px solid darkgray;
}
</style>
@endsection

@section('script')
<script>
	var delete_id = 0;

    $(function() {
	    $("#tshirt").on('change', function(e) {
	        $.post('{{ route('teacher.save_tshirt') }}', { 'tshirt': $(this).val(), '_token': '{{csrf_token()}}' }, function(data) {
                // Flash the tshirt field to show it has been written
                $('#tshirt').stop()
                    .animate({backgroundColor: "#90EE90"}, 500)
                    .animate({backgroundColor: "#FFFFFF"}, 500);
            });
	    });

		$(".video_delete_button").click(function(e) {
			e.preventDefault();
			delete_id = $(this).attr('delete_id');
			$("#video-dialog-confirm").dialog('open');
		});

		$( "#video-dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:200,
			width:500,
			modal: true,
			buttons: {
				"Delete video": function() {
					$( this ).dialog( "close" );
					$('#video_delete_form_' + delete_id).submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

		$(".team_delete_button").click(function(e) {
			e.preventDefault();
			delete_id = $(this).attr('delete_id');
			$("#team-dialog-confirm").dialog('open');
		});

		$( "#team-dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:200,
			width:500,
			modal: true,
			buttons: {
				"Delete Team": function() {
					$( this ).dialog( "close" );
					$('#team_delete_form_' + delete_id).submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

        $('.validate_video').click(function(e) {
            e.preventDefault();
            var video_id = $(this).data('id');
            $.get('/validate_video/' + video_id, function(data) {
                $('#validation_results_' + video_id).remove();
                $('#video_row_' + video_id).after('<tr id="validation_results_' + video_id + '"><td colspan="7">' + data + "</td></tr>");
            });
        });
	});
</script>
@endsection

<?php View::share('skip_title', 1) ?>

@section('before_header')
<div class="info_header">
	<div class="summary_table pull-right" >
		<table class="table table-condensed table-bordered">
			<thead>
				<tr>
					<th>County/District/School</th>
					<th>Teams (Paid For)</th>
					<th>Invoice</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<strong>C:</strong> {{ $school->county }}<br />
						<strong>D:</strong> {{ $school->district }}<br />
						<strong>S:</strong> {{ $school->name }}
					</td>
					<td>
						<strong>Challenge:</strong> {{ $teams->count() }}
							({{ $invoice->team_count }}) <br />
						<strong>Video:</strong> {{ $videos->count() }}
							({{ $invoice->video_count }})<br />
					</td>
					<td class="{{ $paid }}">{{ $paid }}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<h1>Manage Teams and Videos</h1>
	<h2>{{ $school->name }}</h2>
	<div class="clear"></div>
</div>
@endsection

@section('main')
@if($invoice->team_count > 0)
<div class="row">
    <div class="col-sm-6 col-xs-8">
        <h3>T-Shirt Size</h3>
        <p>All Challenge Team Teachers recieve a t-shirt</p>
        <div class="form-group">
            {!! Form::select('tshirt', $tshirt_sizes, $invoice->user->tshirt, [ 'id' => "tshirt", 'class' => 'form-control' ])  !!}
        </div>
    </div>
</div>
@endif

<h3>Manage Challenge Teams</h3>
@if( $teams->count() < $invoice->team_count AND $invoice->team_count > 0)
	@if(/* $invoice->paid == */ 1)
		<p>{{ link_to_route('teacher.teams.create', 'Add Challenge Team',array(), array('class' => 'btn btn-primary')) }}</p>
	@else
		<p>Payment Not Recieved</p>
	@endif
@else
	<p>Team Limit Reached</p>
@endif

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Division</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if ($teams->count())
			@foreach ($teams as $team)
				<tr>
					<td>{{{ $team->name }}}</td>
					<td>{!! join('<br />', $team->student_list()) !!}</td>
					<td>{{ $team->division->longname() }}</td>
	                <td>{{ link_to_route('teacher.teams.edit', 'Edit', array($team->id), array('class' => 'btn btn-info')) }}
	                	&nbsp;
	                    {!! Form::open(array('method' => 'DELETE', 'route' => array('teacher.teams.destroy', $team->id), 'id' => 'team_delete_form_' . $team->id, 'style' => 'display: inline-block;'))  !!}
	                        {!! Form::submit('Delete', array('class' => 'btn btn-danger team_delete_button', 'delete_id' => $team->id))  !!}
	                    {!! Form::close()  !!}
	                </td>
				</tr>
			@endforeach
		@else
			<tr><td colspan="4" class="text-center">No Teams Created</td></tr>
		@endif

	</tbody>
</table>

	<h3>Manage Videos</h3>
	@if( $videos->count() < $invoice->video_count AND $invoice->video_count > 0 )
		@if( /* $invoice->paid == */  1)
			<p>{{ link_to_route('teacher.videos.create', 'Add Video', [], [ 'class' => 'btn btn-primary' ]) }}</p>
		@else
			<p>Payment Not Recieved</p>
		@endif
	@else
		<p>Video Limit Reached</p>
	@endif

	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name/Division</th>
				<th>Students</th>
				<th>YouTube</th>
				<th>Custom Parts</th>
				<th>Files</th>
				<th>Status</th>
				<th class="narrow">Actions</th>
			</tr>
		</thead>

		<tbody>
			@if ($videos->count())
				@foreach ($videos as $video)
					<tr id="video_row_{{ $video->id }}">
						<td>{{{ $video->name }}}<br />{{{ $video->vid_division->longname() }}}</td>
						<td>{!!  join('<br />', $video->student_list()) !!}</td>
						<td><a href="http://youtube.com/watch?v={{{ $video->yt_code }}}" target="_new">YouTube</a></td>
						<td>{{{ $video->has_custom==1 ? 'Yes' : 'No' }}}</td>
						<td>{{ count($video->files) }}</td>
						<td class="{{ ($video->status==VideoStatus::Pass || $video->status == VideoStatus::Warnings) ? 'confirmed' : 'unconfirmed'  }} text-center">
							<span id="video_result_{{ $video->id }}" class="{{ VideoStatus::toClasses($video->status) }}">
                                {{ VideoStatus::getDescription($video->status) }}
                            </span>
						</td>
						<td>
							{{ link_to_route('teacher.videos.show', 'Preview', [$video->id], ['class' => 'btn btn-sm btn-primary']) }}
							&nbsp;
		                    {{ link_to_route('teacher.videos.edit', 'Edit', [$video->id], ['class' => 'btn btn-sm btn-info']) }}
		                    &nbsp;
		                    {{ link_to_route('uploader.index', 'Upload', [$video->id], ['class' => 'btn btn-sm btn-success']) }}
		                    &nbsp;
                            <button data-id="{{ $video->id }}" class="validate_video btn btn-sm btn-warning" title="Validate">
                                Validate
                            </button>

		                    {!! Form::open(['method' => 'DELETE', 'route' => ['teacher.videos.destroy', $video->id], 'id' => 'video_delete_form_' . $video->id, 'style' => 'display: inline-block;'])  !!}
		                        {!! Form::submit('Delete', ['class' => 'btn btn-sm btn-danger video_delete_button', 'delete_id' => $video->id])  !!}
		                    {!! Form::close()  !!}
	                	</td>
					</tr>
				@endforeach
			@else
				<tr><td colspan="7" class="text-center">No Videos Added</td></tr>
			@endif
		</tbody>
	</table>

<div id="video-dialog-confirm" style="display:none;" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

<div id="team-dialog-confirm" title="Delete Team?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This team and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@endsection
