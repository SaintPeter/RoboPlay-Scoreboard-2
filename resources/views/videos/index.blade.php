@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.filtertable.min.js') }}
@endsection

@section('style')
    <style>

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
            <tr id="video_row_{{ $video->id }}">
				<td>{{{ $video->name }}}</td>
				<td>{!! join('<br />', $video->student_list()) !!}</td>
                <td class="{{ ($video->status==VideoStatus::Pass || $video->status == VideoStatus::Warnings) ? 'confirmed' : 'unconfirmed'  }} text-center">
                <span id="video_result_{{ $video->id }}" class="{{ VideoStatus::toClasses($video->status) }}">
                    {{ VideoStatus::getDescription($video->status) }}
                </span>
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
                    <button data-id="{{ $video->id }}" class="validate_video btn btn-warning btn-margin" title="Validate">
                        Validate
                    </button>
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
