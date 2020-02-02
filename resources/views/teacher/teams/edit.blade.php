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
        var initial_div_id = {!! $team->division->id !!};
        comps.change(handle_comp_change);

        // Initial Setting
        handle_comp_change();
        inital_div_id = -1;

        function handle_comp_change() {
          var comp_id = comps.val();
          divs.children().remove();
          if(divisions.hasOwnProperty(comp_id)) {
            divs.removeProp('disabled');
            divs.append('<option value="0">- Select Division -</option>');
            for(var id in divisions[comp_id]) {
              var selected = (id == initial_div_id) ? 'selected' : '';
              var item = '<option value="' + id + '"' + selected + '>' + divisions[comp_id][id] + '</option>';
              divs.append(item);
            }
          } else {
            divs.prop('disabled', true);
            divs.append('<option value="0">Must Select Challenge First</option>')
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
{!! Form::model($team, array('method' => 'PATCH', 'route' => array('teacher.teams.update', $team->id), 'role'=>"form", 'class' => 'col-md-8'))  !!}
    {!! Form::hidden('invoice_id', $invoice->id) !!}
        <div class="form-group">
			{!! Form::label('name', 'Team Name:')  !!}
			{!! Form::text('name',$team->name, array('class'=>'form-control col-md-4'))  !!}
		</div>

        <div class="form-group">
            {!! Form::label('comp_id', 'Competition:')  !!}
            {!! Form::select('comp_id', $comps, $comp_id, [ 'class'=>'form-control col-md-4' ])  !!}
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
				@if(Session::has('students') or !empty($students))
					{{-- For existing students during edit --}}
					@if(Session::has('students'))
						@foreach(Session::get('students') as $index => $student)
							@include('students.partial.create', compact('index', 'ethnicity_list', 'student' ))
						@endforeach
					@endif
					{{-- For new students during edit --}}
					@if(!empty($students))
						@foreach($students as $student)
							<?php $index++; ?>
							@include('students.partial.edit',   compact('index', 'ethnicity_list', 'student' ))
						@endforeach
					@endif
				@else
					<?php $index = -1; ?>
				@endif

			</div>
			<br />
			{!! Form::button('Add Student', [ 'class' => 'btn btn-success', 'id' => 'add_student', 'title' => 'Add Student' ])  !!}
			{!! Form::button('Mass Upload Students', [ 'class' => 'btn btn-success', 'id' => 'mass_upload_students', 'title' => 'Upload Students' ])  !!}
			{!! Form::button('Choose Students', [ 'class' => 'btn btn-success', 'id' => 'choose_students', 'title' => 'Choose Students' ] )  !!}
		</div>

		{!! Form::submit('Submit', array('class' => 'btn btn-primary '))  !!}
		&nbsp;
		{{ link_to_route('teacher.index',   'Cancel', [], ['class' => 'btn btn-info']) }}

{!! Form::close()  !!}

{{-- Update savedIndex with the number of students already displayed . . plus 1 --}}
<script>
	savedIndex = {{ $index + 1 }};
</script>

@include('students.partial.dialogs')

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

@endsection
