@extends('layouts.scaffold')



@section('script')
<script>
    $(document).on('ready', function() {
    now = new Date();
    min = new Date(now.getFullYear(),0,1);
    max = new Date(now.getFullYear(),11,31);
    $( ".date" ).datepicker({
      dateFormat: "yy-mm-dd",
      minDate: min,
      maxDate: max
    });
  });
</script>
@endsection

@section('main')
@if ($errors->any())
<div>
	<h3>Validation Errors</h3>
	<ul>
		{!! implode('', $errors->all('<li class="error">:message</li>'))  !!}
    </ul>
</div>
@endif

{!! Form::model($compyear, ['route' => [ 'compyears.update', $compyear->id ], 'method' => 'PATCH', 'class' => 'col-md-6' ])  !!}
	<div class="row">
    	<div class="form-group col-md-2">
    		{!! Form::label('year', 'Year')  !!}
    		{!! Form::text('year', null, [ 'class'=>'form-control col-md-4' ])  !!}
    	</div>

    	<div class="form-group col-md-6">
    	    {!! Form::label('invoice_type', "Invoice Type")  !!}
    	    {!! Form::select('invoice_type', $invoice_types , null, [ 'class'=>'form-control' ])  !!}
    	</div>

    	<div class="form-group col-md-4">
    	    {!! Form::label('invoice_type_id', "Invoice Type Id")  !!}
    	    {!! Form::text('invoice_type_id', null, [ 'class'=>'form-control col-md-1' ])  !!}
    	</div>
    </div>

    <div class="row">
        <div class="form-group col-md-4">
            {!! Form::label('reminder_start', 'Reminders Start Date:')  !!}
            {!! Form::text('reminder_start', null, [ 'class'=>'form-control date', 'autocomplete' => 'off' ])  !!}
        </div>

        <div class="form-group col-md-4">
            {!! Form::label('reminder_end', 'Reminders End Date:')  !!}
            {!! Form::text('reminder_end', null, [ 'class'=>'form-control date', 'autocomplete' => 'off' ])  !!}
        </div>

        <div class="form-group col-md-4">
            {!! Form::label('edit_end', 'Teacher Editing End Date:')  !!}
            {!! Form::text('edit_end', null, [ 'class'=>'form-control date', 'autocomplete' => 'off' ])  !!}
        </div>
    </div>

	<div class="form-group">
		{!! Form::label('competitions', 'Competitions')  !!}
		{!! Form::select('competitions[]', $competition_list, $comp_selected, [ 'class'=>'form-control', 'multiple' => 'multiple', 'size' => 10 ])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('vid_competitions', 'Video Competitions')  !!}
		{!! Form::select('vid_competitions[]', $vid_competition_list, $vid_selected, [ 'class'=>'form-control', 'multiple' => 'multiple', 'size' => 10 ])  !!}
	</div>

	{!! Form::submit('Submit', array('class' => 'btn btn-primary btn-margin'))  !!}
	{{ link_to_route('compyears.index', 'Cancel', null, [ 'class'=>'btn btn-info btn-margin' ]) }}

{!! Form::close()  !!}



@endsection