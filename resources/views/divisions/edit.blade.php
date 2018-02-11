@extends('layouts.scaffold')

@section('main')
{!! Form::model($division, array('method' => 'PATCH', 'route' => array('divisions.update', $division->id)))  !!}
	<div>
        <div class="form-group">
            {!! Form::label('name', 'Name:')  !!}
            {!! Form::text('name')  !!}
        </div>

        <div>
            {!! Form::label('description', 'Description:')  !!}
            {!! Form::text('description')  !!}
        </div>

        <div class="form-group">
            {!! Form::label('display_order', 'Display_order:')  !!}
            {!! Form::input('number', 'display_order')  !!}
        </div>

        <div class="form-group">
            {!! Form::label('level', 'Level:')  !!}
            {!! Form::input('number', 'level')  !!}
        </div>

        <div class="form-group">
            {!! Form::label('competition_id', 'Competition_id:')  !!}
            {!! Form::select('competition_id', $competitions, $division->competition_id)  !!}
        </div>

		<div class="form-group">
			{!! Form::submit('Update', array('class' => 'btn btn-info'))  !!}
			{{ link_to_route('divisions.show', 'Cancel', $division->id, array('class' => 'btn')) }}
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
