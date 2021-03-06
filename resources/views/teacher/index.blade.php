@extends('layouts.scaffold')

@section('style')
<style>
.Paid, .confirmed {
	background-color: lightgreen !important;
}

.Pending {
    background-color: darkorange !important;
}

.Unpaid, .unconfirmed {
	background-color: pink !important;
}

.Canceled {
    background-color: darkgrey !important;
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
    padding-left: 2em;
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

.validate_pulse {
    animation: VALIDATE_ANIMATION 2s infinite;
}

@keyframes VALIDATE_ANIMATION {
  0% {
      background-color: #f0ad4e;
      border-color: #f0ad4e
  }
  50% {
      background-color: #f04e4e;
      border-color: #f04e4e;
  }
}

</style>
@endsection

@section('script')
<script>
	var delete_id = 0;

    $(document).on('ready', function() {
        $(".video_delete_button").prop('disabled', false);
        $(".team_delete_button").prop('disabled', false);

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
	});
</script>
@include("partials.validate_video")
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
<div class="row">
    <div class="col-md-6">
        <h3>Note</h3>
        @if($reg_days >= 0)
            <p>You have until <strong>{{ $comp_year->reminder_end->format('M jS') }}</strong> (<strong>{{ $reg_days }}</strong> days) to register new Teams and Videos</p>
        @else
            <p class="text-danger">Warning:  Changes to students and t-shirt sizes will not change ordered shirts</p>
        @endif
        @if($competition_error)
            <p class="text-danger">Warning:  Selected Team Divisions are at different sites</p>
        @endif
        @if($validation_error)
            <p class="text-danger">Warning:  One or more videos have not passed validation.
                Please click the "Validate" button by each video and check the output.</p>
        @endif
   </div>
    <div class="col-sm-6 col-xs-8">
    @if($invoice->team_count > 0)
        <h3>T-Shirt Size</h3>
        <p>All Challenge Team Teachers receive a t-shirt</p>
        <div class="row">
            <div class="form-group col-sm-6">
                {!! Form::select('tshirt', $tshirt_sizes, $invoice->user->tshirt, [ 'id' => "tshirt", 'class' => 'form-control' ])  !!}
            </div>
        </div>
    @endif
    </div>
</div>

<h3>Manage Challenge Teams</h3>
@if($reg_days >= 0)
        @if($invoice->paid == 1 || $invoice->paid == 2)
            @if( $teams->count() < $invoice->team_count AND $invoice->team_count > 0)
                <p>{{ link_to_route('teacher.teams.create', 'Add Challenge Team',array(), array('class' => 'btn btn-primary')) }}</p>
            @else
                <p>Team Limit Reached</p>
            @endif
        @elseif($invoice->paid == 3)
            <p>Invoice Canceled</p>
        @else
            <p>Payment Not Received</p>
        @endif
@else
    <p>No new teams may be added after {{ $comp_year->reminder_end->format('M d') }}</p>
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
					<td {{ $competition_error ? 'class=danger' : '' }}>
                        {{ $team->division->longname() }}
                    </td>
	                <td>
                        @if($edit_days >= 0)
                        {{ link_to_route('teacher.teams.edit', 'Edit', array($team->id), array('class' => 'btn btn-info')) }}
                        @endif
	                	&nbsp;
	                    {!! Form::open(array('method' => 'DELETE', 'route' => array('teacher.teams.destroy', $team->id), 'id' => 'team_delete_form_' . $team->id, 'style' => 'display: inline-block;'))  !!}
	                        {!! Form::submit('Delete', array('class' => 'btn btn-danger team_delete_button', 'delete_id' => $team->id, 'disabled' => 'disabled'))  !!}
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
@if($reg_days >= 0)
        @if($invoice->paid == 1 || $invoice->paid == 2)
	        @if( $videos->count() < $invoice->video_count AND $invoice->video_count > 0 )
			    <p>{{ link_to_route('teacher.videos.create', 'Add Video', [], [ 'class' => 'btn btn-primary' ]) }}</p>
            @else
                <p>Video Limit Reached</p>
            @endif
        @elseif($invoice->paid == 3)
            <p>Invoice Canceled</p>
		@else
			<p>Payment Not Received</p>
		@endif
@else
    <p>No new videos may be added after {{ $comp_year->reminder_end->format('M d') }}</p>
@endif

	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name/Comp/Div</th>
				<th>Students</th>
				<th>YouTube</th>
				<th>Custom Parts</th>
				<th>Files</th>
				<th>Validation</th>
                <th>Review</th>
				<th class="narrow">Actions</th>
			</tr>
		</thead>

		<tbody>
			@if ($videos->count())
				@foreach ($videos as $video)
					<tr id="video_row_{{ $video->id }}">
						<td>
                            {{{ $video->name }}}<br />
                            {{{ $video->vid_division->competition->name }}}<br>
                            {{{ $video->vid_division->name }}}
                        </td>
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
                            {{ VideoReviewStatus::getDescription($video->review_status) }}
                        </td>
						<td>
							{{ link_to_route('teacher.videos.show', 'Preview', [$video->id], ['class' => 'btn btn-sm btn-primary']) }}
							&nbsp;
                            @if($edit_days >= 0)
                                {{ link_to_route('teacher.videos.edit', 'Edit', [$video->id], ['class' => 'btn btn-sm btn-info']) }}
		                    &nbsp;
		                        {{ link_to_route('uploader.index', 'Upload', [$video->id], ['class' => 'btn btn-sm btn-success']) }}
                            @endif
		                    &nbsp;<br>
                            <button data-id="{{ $video->id }}" class="validate_video btn btn-sm btn-warning {{ $video->status!=VideoStatus::Pass ? 'validate_pulse' : '' }}" title="Validate">
                                Validate
                                <i id="spinner_{{ $video->id }}" class="fa fa-spinner fa-pulse fa-fw" style="display: none"></i>
                            </button>
                            &nbsp;
		                    {!! Form::open(['method' => 'DELETE', 'route' => ['teacher.videos.destroy', $video->id], 'id' => 'video_delete_form_' . $video->id, 'style' => 'display: inline-block;'])  !!}
		                        {!! Form::submit('Delete', ['class' => 'btn btn-sm btn-danger video_delete_button btn-margin', 'delete_id' => $video->id, 'disabled' => 'disabled'])  !!}
		                    {!! Form::close()  !!}
	                	</td>
					</tr>
				@endforeach
			@else
				<tr><td colspan="8" class="text-center">No Videos Added</td></tr>
			@endif
		</tbody>
	</table>

<div id="video-dialog-confirm" style="display: none;" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

<div id="team-dialog-confirm" title="Delete Team?" style="display: none;">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This team and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@endsection
