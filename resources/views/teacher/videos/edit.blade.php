@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.form.min.js') }}
@endsection

@section('style')
<style>
/* Fix margins for nested inline forms */
.form-inline .form-group{
	margin-left: 0;
	margin-right: 0;
}

.vertical-container {
	display: table;
	width: 100%;
}

.vertical-container > .col-md-1 {
	display: table-cell;
	vertical-align: middle;
	height: 100%;
	float: none;
}

.indent {
	margin-left: 20px;
}
</style>
@endsection

@include('students.partial.js', [ 'type' => 'videos' ])

@section('main')
{!! Form::model($video, array('method' => 'PATCH', 'route' => array('teacher.videos.update', $video->id), 'class' => 'col-md-8'))  !!}
    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}" />
	<div class="form-group">
	    {!! Form::label('name', 'Name:')  !!}
	    {!! Form::text('name', $video->name, [ 'class'=>'form-control col-md-4' ])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('vid_division_id', 'Video Division:')  !!}
		{!! Form::select('vid_division_id', $division_list, null, [ 'class'=>'form-control col-md-4' ])  !!}
	</div>

	<div class="form-group">
	    {!! Form::label('yt_code', 'YouTube URL or Code:')  !!}
	    {!! Form::text('yt_code', $video->yt_code, [ 'class'=>'form-control col-md-4' ])  !!}
	    <label>Accepted Formats:</label>
	    <ul>
	    	<li>http://www.youtube.com/watch?v=-wtIMTCHWuI</li>
			<li>http://www.youtube.com/v/-wtIMTCHWuI</li>
			<li>http://youtu.be/-wtIMTCHWuI</li>
			<li>-wtIMTCHWuI (Just the code)</li>
	    </ul>
	</div>

{!! Form::label('',"Content Tags")  !!}
	<div class="indent">
		<div class="checkbox">
			<label>
				{!! Form::hidden('has_story', 0)  !!}
				{!! Form::checkbox('has_story', 1, $video->has_story)  !!} Storyline
			</label>
		</div>

		<div class="checkbox">
			<label>
				{!! Form::hidden('has_choreo', 0)  !!}
				{!! Form::checkbox('has_choreo', 1, $video->has_choreo)  !!} Choreography
			</label>
		</div>

		<div class="checkbox">
			<label>
				{!! Form::hidden('has_task', 0)  !!}
				{!! Form::checkbox('has_task', 1, $video->has_task)  !!} Interesting Task
			</label>
		</div>

        <div class="checkbox">
            <label>
                {!! Form::hidden('has_theme', 0)  !!}
                {!! Form::checkbox('has_theme',1, $video->has_theme)  !!} Yearly Theme
            </label>
        </div>

        <div class="checkbox">
            <label>
                {!! Form::hidden('has_custom', 0)  !!}
                {!! Form::checkbox('has_custom',1, $video->has_custom)  !!} Custom Designed Part
                <sup><a href="#custom">[1]</a></sup>
            </label>
        </div>

        <div class="checkbox">
            <label>
                {!! Form::hidden('has_advanced', 0)  !!}
                {!! Form::checkbox('has_advanced', 1, $video->has_advanced)  !!} Advanced Electronics
                <sup><a href="#advanced">[2]</a></sup>
            </label>
        </div>
	</div>
    <label>Notes</label>
    <div style="margin-left: 10px;">
        <p>These tags act as hints to judges about the content of your videos.  <br />
            Each video will be scored on all areas regardless of tags, except for "Custom part" and "Advanced Electronics", <br />
            which must be flagged to be judged for those categories.<br/>
            Computational Thinking will automatically be tagged when the video's code is uploaded.</p>
        <p id="custom">[1] Videos with Custom Parts have additional file requriements.  Please review the CFP before tagging.</p>
        <p id="advanced">[2] Advanced Electronics videos must feature Rasberry Pi or Ardunio parts and code in .ch format.</p>
    </div>
	@include('students.partial.fields', [ 'students' => $students ])

	<div class="form-group">
		{!! Form::submit('Update', array('class' => 'btn btn-primary'))  !!}
		 		&nbsp;
		{{ link_to_route('teacher.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

	</div>
{!! Form::close()  !!}

@include('students.partial.dialogs')

@if ($errors->any())
    <div class="col-md-4">
        <h3>Validation Errors</h3>
        <ul>
            {!! implode($errors->all('<li class="error">:message</li>')) !!}
        </ul>
    </div>
@endif

@endsection
