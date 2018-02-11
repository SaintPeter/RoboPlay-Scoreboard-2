@extends('layouts.scaffold')

@section('main')
{!! Form::open(array('route' => 'divisions.store'))  !!}
	<div>
        <div class="form-group">
            {!! Form::label('name', 'Name')  !!}
            {!! Form::text('name')  !!}
        </div>

        <div class="form-group">
            {!! Form::label('description', 'Description')  !!}
            {!! Form::text('description')  !!}
        </div>

        <div class="form-group">
            {!! Form::label('display_order', 'Display Order')  !!}
            {!! Form::input('number', 'display_order')  !!}
        </div>

		<div class="form-group">
            {!! Form::label('level', 'Level:')  !!}
            {!! Form::input('number', 'level')  !!}
        </div>

        <div class="form-group">
            {!! Form::label('competition_id', 'Competition')  !!}
            {!! Form::select('competition_id', $competitions)  !!}
        </div>

		<div class="form-group">
			{!! Form::submit('Submit', array('class' => 'btn btn-info'))  !!}
		</div>
	</div>
{!! Form::close()  !!}

@if ($errors->any())
<div class="col-md-6">
	<h3>Vadivdation Errors</h3>
	<div>
		{{ implode('', $errors->all('<div class="error">:message</div>')) }}
	</div>
</div>
@endif

@endsection


