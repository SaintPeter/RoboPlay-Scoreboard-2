@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('css/lytebox.css') }}
	{{ HTML::script('js/lytebox.js') }}
@endsection

@section('style')
<style>
.video_container {
	float: left;
	width: 640;
	postition: relative;
	padding-left: 15px;
	padding-right: 15px;
}
</style>
@endsection

@section('script')
<script>
	$(function() {
		$("#delete_button").click(function(e) {
			e.preventDefault();
			$("#dialog-confirm").dialog('open');
		});

		$( "#dialog-confirm" ).dialog({
			resizable: false,
			autoOpen: false,
			height:180,
			width:500,
			modal: true,
			buttons: {
				"Delete Video": function() {
					$( this ).dialog( "close" );
					$("#delete_form").submit();
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
<p>{{ link_to_route('teacher.index', 'Return to Team Management' ,[], ['class' => 'btn btn-info']) }}</p>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Students</th>
			<th>Custom Parts</th>
			<th>Uploads</th>
			<th>County/District/School</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $video->name }}}</td>
			<td>{{ join('<br />', $video->student_list()) }}</td>
			<td>{{{ $video->has_custom==1 ? 'Has Custom Parts' : 'No Custom Parts' }}}</td>
			<td class="{{ $video->has_vid==1 ? 'confirmed' : 'unconfirmed' }}">
                @if($video->has_vid==1)
                    <span class="btn btn-success btn-xs btn-margin">Has Video</span>
                @else
                    <span class="btn btn-danger btn-xs btn-margin">No Video</span>
                @endif
                <br>
                @if($video->has_code==1)
                    <span class="btn btn-info btn-xs btn-margin">Has Code</span>
                @else
                    <span class="btn btn-danger btn-xs btn-margin">No Code</span>
                @endif
				</td>

			<td>
				@if(isset($video->school))
					<strong>C:</strong> {{ $video->school->county }}<br />
					<strong>D:</strong> {{ $video->school->district }}<br />
					<strong>S:</strong> {{ $video->school->name }}
				@else
					Not Set
				@endif
			</td>
            <td>
            	{{ link_to_route('teacher.videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}
				&nbsp;
                {{ link_to_route('uploader.index', 'Upload', array($video->id), array('class' => 'btn btn-success')) }}
				&nbsp;
                {!! Form::open(array('method' => 'DELETE', 'route' => array('teacher.videos.destroy', $video->id), 'id' => 'delete_form_' . $video->id, 'style' => 'display: inline-block;'))  !!}
                    {!! Form::submit('Delete', array('class' => 'btn btn-danger delete_button', 'delete_id' => $video->id))  !!}
                {!! Form::close()  !!}
            </td>
		</tr>
	</tbody>
</table>


<h3>Preview</h3>

@include('partials.showvideo', [ 'video' => $video, 'show_division' => false ])

@include('partials.filelist', [ 'video' => $video, 'show_type' => true, 'allow_edit' => true ])

<div id="dialog-confirm" title="Delete video?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
	This video and all scores will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>


@endsection
