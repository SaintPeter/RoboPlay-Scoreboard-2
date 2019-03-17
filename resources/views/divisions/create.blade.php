@extends('layouts.scaffold')

@section('main')
{!! Form::open(['route' => 'divisions.store', 'class' => 'col-md-6'])  !!}
    <div class="row">
        <div class="form-group col-md-6">
            {!! Form::label('name', 'Name:')  !!}
            {!! Form::text('name', null, [ 'class'=>'form-control ' ])  !!}
        </div>

        <div class="form-group col-md-6">
            {!! Form::label('description', 'Description:')  !!}
            {!! Form::text('description', null, [ 'class'=>'form-control ' ])  !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            {!! Form::label('display_order', 'Display Order:')  !!}
            {!! Form::input('number', 'display_order', null, [ 'class'=>'form-control numeric' ])  !!}
        </div>

        <div class="form-group col-md-6">
            {!! Form::label('level', 'Level:')  !!}
            {!! Form::input('number', 'level', null, [ 'class'=>'form-control numeric' ])  !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            {!! Form::label('competition_id', 'Competition:')  !!}
            {!! Form::select('competition_id', $competitions, null, [ 'class'=>'form-control' ])  !!}
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-6">
            {{ link_to_route('divisions.index', 'Cancel', null, array('class' => 'btn btn-info')) }}
            {!! Form::submit('Save', array('class' => 'btn btn-primary'))  !!}
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


