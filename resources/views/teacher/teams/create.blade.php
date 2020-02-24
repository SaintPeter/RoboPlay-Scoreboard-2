@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.form.min.js') }}
@endsection

@section('script')
<script>
    var divisions = {!! $divisions_json !!} ;

    $(document).ready(function() {
      var comps = $('#comp_id');
      var divs = $('#division_select');
      comps.change(handle_comp_change);

      function handle_comp_change() {
          var comp_id = comps.val();
          divs.children().remove();
          if(divisions.hasOwnProperty(comp_id)) {
            divs.removeProp('disabled');
            divs.append('<option value="0">- Select Division -</option>');
            for(var id in divisions[comp_id]) {
              var item = '<option value="' + id + '">' + divisions[comp_id][id] + '</option>';
              divs.append(item);
            }
          } else {
            divs.prop('disabled', true);
            divs.append('<option value="0">Select Competition First</option>')
          }
      }
    })
</script>
@endsection

@section('style')
<style>
/* Fix margins for nested inline forms */
.form-inline .form-group{
	margin-left: 0;
	margin-right: 0;
}

.vertical-container {
	display: table;
	width: 100%;
}

.vertical-container > .col-md-1 {
	display: table-cell;
	vertical-align: middle;
	height: 100%;
	float: none;
}
</style>
@endsection

@include('students.partial.js', [ 'type' => 'teams', 'limit_student_count' => 5 ])

@section('main')
{!! Form::open(array('route' => 'teacher.teams.store', 'role'=>"form", 'class' => 'col-md-8'))  !!}
        {!! Form::hidden('invoice_id', $invoice->id) !!}
		<div class="form-group">
			{!! Form::label('name', 'Team Name:')  !!}
			{!! Form::text('name','', array('class'=>'form-control col-md-4'))  !!}
            <p style="margin-left: 5px">Please do not use school names in team names. School names are already displayed on the scoreboard.</p>
		</div>

        <div class="form-group">
            {!! Form::label('comp_id', 'Competition:')  !!}
            {!! Form::select('comp_id', $comps, null, [ 'class'=>'form-control col-md-4' ])  !!}
        </div>

		<div class="form-group">
			{!! Form::label('division_id', 'Division:')  !!}
            <select name="division_id" id="division_select" disabled="disabled" class="form-control col-md-4">
                <option value="0">Select Competition First</option>
            </select>
		</div>

		<div class="form-group">
			{!! Form::label('student_form', 'Students:')  !!}
			<div class="form-inline" id="student_form">
				@if(Session::has('students'))
					@foreach(Session::get('students') as $index => $student)
						@include('students.partial.create', compact('index', 'ethnicity_list', 'student' ))
					@endforeach
				@else
					<?php $index = -1; ?>
				@endif
			</div>
			<br />
			{!! Form::button('Add Student', [ 'class' => 'btn btn-success', 'id' => 'add_student', 'title' => 'Add Student' ])  !!}
			{!! Form::button('Mass Upload Students', [ 'class' => 'btn btn-success', 'id' => 'mass_upload_students', 'title' => 'Upload Students' ])  !!}
			{!! Form::button('Choose Students', [ 'class' => 'btn btn-success', 'id' => 'choose_students', 'title' => 'Choose Students'])  !!}
		</div>

		{!! Form::submit('Submit', array('class' => 'btn btn-primary '))  !!}
		&nbsp;
		{{ link_to_route('teacher.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

{!! Form::close()  !!}

{{-- Update savedIndex with the number of students already displayed . . plus 1 --}}
<script>
	savedIndex = {{ $index + 1 }};
</script>

@include('students.partial.dialogs')

@if ($errors->any())
    <div class="col-md-4">
        <h3>Validation Errors</h3>
        <ul>
            {!! implode($errors->all('<li class="error">:message</li>')) !!}
        </ul>
    </div>
@endif

@endsection


