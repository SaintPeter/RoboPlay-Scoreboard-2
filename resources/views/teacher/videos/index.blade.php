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

</style>
@endsection

@section('script')
<script>
	var delete_id = 0;
	$(function() {
		$(".delete_button").click(function(e) {
			e.preventDefault();
			delete_id = $(this).attr('delete_id');
			$("#dialog-confirm").dialog('open');
		});

		$( "#dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:180,
			width:500,
			modal: true,
			buttons: {
				"Delete video": function() {
					$( this ).dialog( "close" );
					$('#delete_form_' + delete_id).submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
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
					<th>Challenge/Division</th>
					<th>Videos (Used)</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<strong>C:</strong> {{ $invoice->school->county }}<br />
						<strong>D:</strong> {{ $invoice->school->district }}<br />
						<strong>S:</strong> {{ $invoice->school->name }}
					</td>
					<td>
						@if(isset($invoice->vid_division))
							{{ $invoice->vid_division->competition->name }}<br />
							{{ $invoice->vid_division->name }}
						@else
							No Division Set
						@endif
					</td>
					<td class="text-center">{{ $invoice->video_count }} ({{ $videos->count() }})</td>
					<td class="{{ $paid }}">{{ $paid }}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<h1>Challenge Videos</h1>
	<h2>{{ $school->name }}</h2>
	<div class="clear"></div>
</div>
@endsection


@section('main')

@if(isset($invoice->vid_division))
	@if( true ) {{-- $videos->count() < $invoice->video_count AND $invoice->video_count > 0 --}}
		@if($invoice->paid == 1)
			<p>{{ link_to_route('teacher.videos.create', 'Add Video', [], [ 'class' => 'btn btn-primary' ]) }}</p>
		@else
			<p>Payment Not Recieved</p>
		@endif
	@else
		<p>Video Limit Reached</p>
	@endif
@else
	<p>Video Division not Set</p>
@endif

	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Students</th>
				<th>YouTube</th>
				<th>Custom Parts</th>
				<th>Files</th>
				<th>Uploads</th>
				<th class="narrow">Actions</th>
			</tr>
		</thead>

		<tbody>
			@if ($videos->count())
				@foreach ($videos as $video)
					<tr>
						<td>{{{ $video->name }}}</td>
						<td>{{ $video->student_count() }}</td>
						<td><a href="http://youtube.com/watch?v={{{ $video->yt_code }}}" target="_new">YouTube</a></td>
						<td>{{{ $video->has_custom==1 ? 'Yes' : 'No' }}}</td>
						<td>{{ count($video->files) }}</td>
						<td class="{{ $video->has_vid==1 ? 'confirmed' : 'unconfirmed' }}">
							{{ $video->has_vid==1 ? 'Video File' : 'No Video' }} <br />
							{{ $video->has_code==1 ? 'Code File' : 'No Code' }} <br />
						</td>
						<td>
							{{ link_to_route('teacher.videos.show', 'Preview', array($video->id), array('class' => 'btn btn-primary')) }}
							&nbsp;
		                    {{ link_to_route('teacher.videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}
		                    &nbsp;
		                    {{ link_to_route('uploader.index', 'Upload', array($video->id), array('class' => 'btn btn-success')) }}
		                    &nbsp;
		                    {!! Form::open(array('method' => 'DELETE', 'route' => array('teacher.videos.destroy', $video->id), 'id' => 'delete_form_' . $video->id, 'style' => 'display: inline-block;'))  !!}
		                        {!! Form::submit('Delete', array('class' => 'btn btn-danger delete_button', 'delete_id' => $video->id))  !!}
		                    {!! Form::close()  !!}
	                	</td>
					</tr>
				@endforeach
			@else
				<tr><td colspan="5">No Videos Added</td></tr>
			@endif
		</tbody>
	</table>
<p><strong>Note: </strong>Due to a bug, you will see all videos from your school/site.  Because of this, the video count limits have been suspended.  <br />
	<span style="color: red">Do <strong>NOT</strong> delete vidoes which are not yours.</span>
	</p>

<div id="dialog-confirm" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

@endsection
