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

{!! Form::model($vid_division, array('method' => 'PATCH', 'route' => array('vid_divisions.update', $vid_division->id), 'class' => 'col-md-6'))  !!}
	<div class="form-group">
		{!! Form::label('name', 'Name:')  !!}
		{!! Form::text('name')  !!}
	</div>

	<div class="form-group">
		{!! Form::label('description', 'Description:')  !!}
		{!! Form::text('description')  !!}
	</div>

	<div class="form-group">
		{!! Form::label('display_order', 'Display Order:')  !!}
		{!! Form::input('number', 'display_order')  !!}
	</div>

	<div class="form-group">
		{!! Form::label('competition_id', 'Video Competition:')  !!}
		{!! Form::select('competition_id', $competitions, $vid_division->competition_id)  !!}
	</div>

	<div class="form-group">
		{!! Form::submit('Update', array('class' => 'btn btn-info'))  !!}
		{{ link_to_route('vid_divisions.show', 'Cancel', $vid_division->id, array('class' => 'btn')) }}
	</div>
{!! Form::close()  !!}
@endsection
