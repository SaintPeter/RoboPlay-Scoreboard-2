@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('css/bootstrap-timepicker.min.css') }}
	{{ HTML::script('js/bootstrap-timepicker.min.js') }}
@endsection

@section('script')
<script>
	$(function() {
		$( "#event_date" ).datepicker({ dateFormat: "yy-mm-dd" });
		$( "#freeze_time" ).timepicker();
	});
</script>
@endsection

@section('main')
{!! Form::open(array('route' => 'competitions.store', 'class' => 'col-md-6'))  !!}
	<div class="form-group">
	    {!! Form::label('name', 'Name:')  !!}
	    {!! Form::text('name', null, [ 'class'=>'form-control col-md-4' ])  !!}
	</div>

	<div class="form-group">
	    {!! Form::label('description', 'Description:')  !!}
	    {!! Form::textarea('description', null, [ 'class'=>'form-control col-md-4' ])  !!}
	</div>

	<div class="form-group">
	    {!! Form::label('location', 'Location:')  !!}
	    {!! Form::text('location', null, [ 'class'=>'form-control col-md-4' ])  !!}
	</div>

	<div class="form-group">
	    {!! Form::label('address', 'Address:')  !!}
	    {!! Form::textarea('address', null, [ 'class'=>'form-control col-md-4' ])  !!}
	</div>

	<div class="form-group">
	    {!! Form::label('event_date', 'Event Date:')  !!}
	    {!! Form::text('event_date', null, [ 'class'=>'form-control date' ])  !!}
	</div>

	<div class="form-group">
	    {!! Form::label('color', 'Color:')  !!}
	    {!! Form::text('color', null, [ 'class'=>'form-control' ])  !!}
	</div>

	<div class="form-group row">
		<div class="col-md-6">
		 	{!! Form::label('freeze_time', 'Freeze Time:')  !!}
			 <div class="input-group bootstrap-timepicker">
				<input name="freeze_time" id="freeze_time" type="text" data-minute-step="5" class="form-control">
				<span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
			</div>
		</div>
		<div class="col-md-6">
			{!! Form::label('frozen', 'Freeze Status:')  !!}
	    	{!! Form::select('frozen', [ 0 => 'Not Frozen', 1 => 'Frozen' ], null, [ 'class'=>'form-control col-md-3' ])  !!}
		</div>
	</div>


	<div class="form-group row">
		<div class="col-md-6">
			{!! Form::label('active', 'Active:')  !!}
			{!! Form::select('active', [ 1 => 'Active', 0 => 'Not Active' ], null, [ 'class'=>'form-control col-md-2' ])  !!}
		</div>
	</div>

	{!! Form::submit('Submit', array('class' => 'btn btn-primary btn-margin'))  !!}
	{{ link_to_route('competitions.index', 'Cancel', null, [ 'class'=>'btn btn-info btn-margin' ]) }}

{!! Form::close()  !!}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
	</ul>
</div>
@endif

@endsection


