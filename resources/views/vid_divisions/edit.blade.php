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

{!! Form::model($vid_division, array('method' => 'PATCH', 'route' => array('vid_divisions.update', $vid_division->id), 'class' => 'col-md-4'))  !!}
	<div class="form-group">
		{!! Form::label('name', 'Name:')  !!}
		{!! Form::text('name',null , ['class' => 'form-control'])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('description', 'Description:')  !!}
		{!! Form::text('description', null, ['class' => 'form-control'])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('display_order', 'Display Order:')  !!}
		{!! Form::input('number', 'display_order', null, ['class' => 'form-control'])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('competition_id', 'Video Competition:')  !!}
		{!! Form::select('competition_id', $competitions, $vid_division->competition_id, ['class' => 'form-control'])  !!}
	</div>

	<div class="form-group">
		{!! Form::submit('Update', array('class' => 'btn btn-info'))  !!}
		{{ link_to_route('vid_divisions.show', 'Cancel', $vid_division->id, array('class' => 'btn btn-default')) }}
	</div>
{!! Form::close()  !!}
@endsection
