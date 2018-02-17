@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.filtertable.min.js') }}
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

		$("#video_table").filterTable();
	});
</script>
@endsection

@section('main')
@include('partials.year_select')
<p>{{ link_to_route('videos.create', 'Add Video', [],  [ 'class' => 'btn btn-primary' ]) }}</p>



<table class="table table-striped table-bordered" id="video_table">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Status</th>
			<th>Teacher/County/District/School</th>
			<th>Challenge/Division</th>
			<th>Year</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
	@if($videos->count())
		@foreach ($videos as $video)
			<tr>
				<td>{{{ $video->name }}}</td>
				<td>{!! join('<br />', $video->student_list()) !!}</td>
				<td>
					{!! $video->has_custom==1 ? '<span class="btn btn-warning btn-xs">Custom</span>' : '' !!} <br />
					{!! $video->has_vid==1 ? '<span class="btn btn-success btn-xs">Video File</span>' : '<span class="btn btn-danger btn-xs">No Video</span>' !!} <br />
					{!! $video->has_code==1 ? '<span class="btn btn-info btn-xs">Code</span>' : '<span class="btn btn-danger btn-xs">No Code</span>' !!} <br />
				</td>
				<td>
					@if($video->teacher)
						{{ $video->teacher->name }}<br />
					@else
						No Teacher Set<br />
					@endif
					@if(isset($video->school))
						<strong>C:</strong> {{ $video->school->county }}<br />
						<strong>D:</strong> {{ $video->school->district }}<br />
						<strong>S:</strong> {{ $video->school->name }}
					@else
						Not Set
					@endif
				</td>
				<td>
					@if(isset($video->vid_division))
						{{ $video->vid_division->competition->name }}<br />
						{{ $video->vid_division->name }}
					@else
						No Division Set
					@endif
				</td>
				<td>
					{{ $video->year }}
				</td>
                <td>
                	{{ link_to_route('videos.show', 'Show', array($video->id), array('class' => 'btn btn-default btn-margin')) }}
                	{{ link_to_route('videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info btn-margin')) }}
					{{ link_to_route('videos.uploader', 'Upload', array($video->id), array('class' => 'btn btn-success btn-margin')) }}
                    {!! Form::open(array('method' => 'DELETE', 'route' => array('videos.destroy', $video->id), 'id' => 'delete_form_' . $video->id, 'style' => 'display: inline-block;'))  !!}
                        {!! Form::submit('Delete', array('class' => 'btn btn-danger delete_button btn-margin', 'delete_id' => $video->id))  !!}
                    {!! Form::close()  !!}
                </td>
			</tr>
		@endforeach
	@else
		<tr><td colspan="7">No Videos Entered</td></tr>
	@endif
	</tbody>
</table>

<div id="dialog-confirm" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
@endsection
