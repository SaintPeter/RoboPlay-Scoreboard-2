@extends('layouts.scaffold')

@section('main')
@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<div class="form-group">
		{{ implode('', $errors->all('<div class="error">:message</div>')) }}
	</div>
</div>
@endif

{!! Form::open(array('route' => 'vid_divisions.store', 'class' => 'col-md-4'))  !!}
	<div class="form-group">
		{!! Form::label('name', 'Name:')  !!}
		{!! Form::text('name',null, ['class' => 'form-control col-md-4'])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('description', 'Description:')  !!}
		{!! Form::text('description',null, ['class' => 'form-control col-md-4'])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('display_order', 'Display Order:')  !!}
		{!! Form::input('number', 'display_order', null, ['class' => 'form-control col-md-4'])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('competition_id', 'Video Competition:')  !!}
		{!! Form::select('competition_id', $competitions, null, ['class' => 'form-control col-md-4'])  !!}
	</div>

	<div class="form-group">
		{!! Form::submit('Submit', array('class' => 'btn btn-info'))  !!}
	</div>

{!! Form::close()  !!}
@endsection


