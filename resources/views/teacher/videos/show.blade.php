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

        $('.validate_video').click(function(e) {
            e.preventDefault();
            var video_id = $(this).data('id');
            $.get('/validate_video/' + video_id, function(data) {
                $('#validation_results_' + video_id).remove();
                $('#video_row_' + video_id).after('<tr id="validation_results_' + video_id + '"><td colspan="8">' + data + "</td></tr>");
            });
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
			<th>Validation</th>
            <th>Review</th>
			<th>County/District/School</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
        <tr id="video_row_{{ $video->id }}">
			<td>{{{ $video->name }}}</td>
			<td>{!! join('<br />', $video->student_list()) !!}</td>
			<td>{{{ $video->has_custom==1 ? 'Has Custom Parts' : 'No Custom Parts' }}}</td>
            <td class="{{ ($video->status==VideoStatus::Pass || $video->status == VideoStatus::Warnings) ? 'confirmed' : 'unconfirmed'  }} text-center">
                <span id="video_result_{{ $video->id }}" class="{{ VideoStatus::toClasses($video->status) }}">
                    {{ VideoStatus::getDescription($video->status) }}
                </span>
            </td>
            <td>
                {{ VideoReviewStatus::getDescription($video->review_status) }}
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
                @if($edit_days >= 0)
                    {{ link_to_route('teacher.videos.edit', 'Edit', array($video->id), array('class' => 'btn btn-info')) }}
                    &nbsp;
                    {{ link_to_route('uploader.index', 'Upload', array($video->id), array('class' => 'btn btn-success')) }}
                @endif

                <button data-id="{{ $video->id }}" class="validate_video btn btn-warning btn-margin" title="Validate">
                    Validate
                </button>
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
