@extends('layouts.scaffold')

@section('script')
    @include('vid_competitions.js')
@endsection

@section('main')
{!! Form::open(array('route' => 'vid_competitions.store', ' class' => 'form-horizontal col-md-4'))  !!}
	<div class="form-group">
	    {!! Form::label('name', 'Name')  !!}
	    {!! Form::text('name', null, [ 'class'=>'form-control col-md-4' ])  !!}
	</div>

    <div class="row">
        <div class="form-group col-md-6">
            <label for="filter">Video Coordinator</label>
            <i id="spinner" class="fa fa-spinner fa-pulse fa-fw" style="display: none;"></i>
            <input placeholder="type to filter" id="filter" class="form-control" />
        </div>

        <div class="form-group col-md-12">
            <select id="user_list" name="user_id" class="form-control">
                <option value="">-- None --</option>
            </select>
        </div>
    </div>

	<div class="form-group">
	    {!! Form::label('event_start', 'Start Date')  !!}
	    {!! Form::text('event_start',null, [ 'class'=>'form-control col-md-4 date' ])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('event_end', 'End Date')  !!}
	    {!! Form::text('event_end', null, [ 'class'=>'form-control col-md-4 date' ])  !!}
	</div>

	{!! Form::submit('Submit', array('class' => 'btn btn-primary btn-margin'))  !!}
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


