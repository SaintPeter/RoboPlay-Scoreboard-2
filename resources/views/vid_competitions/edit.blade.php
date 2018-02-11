@extends('layouts.scaffold')

@section('script')
<script>
	 $(function() {
		$( ".date" ).datepicker({ dateFormat: "yy-mm-dd" });
	});
</script>
@endsection

@section('main')
{!! Form::model($vid_competition, array('method' => 'PATCH', 'route' => array('vid_competitions.update', $vid_competition->id),'class' => 'form-horizontal col-md-4'))  !!}
	<div class="form-group">
	    {!! Form::label('name', 'Name:')  !!}
	    {!! Form::text('name', null, [ 'class'=>'form-control col-md-4' ])  !!}
	</div>

	<div class="form-group">
	    {!! Form::label('event_start', 'Start Date')  !!}
	    {!! Form::text('event_start',null, [ 'class'=>'form-control col-md-4 date' ])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('event_end', 'End Date')  !!}
	    {!! Form::text('event_end', null, [ 'class'=>'form-control col-md-4 date' ])  !!}
	</div>

	{!! Form::submit('Update', array('class' => 'btn btn-primary btn-margin'))  !!}
	{{ link_to_route('vid_competitions.index', 'Cancel',  null, array('class' => 'btn btn-info btn-margin')) }}
{!! Form::close()  !!}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

@endsection
