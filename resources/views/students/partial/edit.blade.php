<!-- student.partial.edit -->
@inject('math_level', "App\Models\Math_Level")
<div class="vertical-container row student_{{$index}}">
	{!! Form::hidden("students[$index][id]", $student->id, [ 'class' => 'student_id' ])  !!}
	@if($index > 0)
		<hr>
	@endif
	<div class="col-md-12">
		<div class="form-group">
			<label class="sr-only" for="students[{{$index}}][first_name]">First Name</label>
			<input type="text" class="form-control" id="students[{{$index}}][first_name]" name="students[{{$index}}][first_name]" placeholder="First Name" value="{{ $student->first_name }}">
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{$index}}][first_name]">Middle/Nick Name</label>
			<input type="text" class="form-control" id="students[{{ $index }}][middle_name]" name="students[{{ $index }}][middle_name]" name="students[{{ $index }}][first_name]" placeholder="Middle/Nick" value="{{ $student->middle_name }}">
		</div>
		<div class="checkbox">
		    <label>
		    	<input type="hidden" name="students[{{ $index }}][nickname]" value="0">
		    	<input type="checkbox" name="students[{{ $index }}][nickname]" value = "1" {{ $student->nickname ? 'checked="checked"' : '' }}> Is Nickname
		    </label>
		  </div>
		<div class="form-group">
			<label class="sr-only" for="students[{{$index}}][last_name]">Last Name</label>
			<input type="text" class="form-control" id="students[{{ $index }}][last_name]" name="students[{{ $index }}][last_name]" placeholder="Last Name" value="{{ $student->last_name }}">
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{$index}}][gender]">Gender</label>
			{!! Form::select("students[$index][gender]", [ 0 => '- Pick Gender -', 'Male' => 'Male', 'Female' => 'Female' ], $student->gender, [ 'class' => 'form-control' ] )  !!}
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{$index}}][ethnicity_id]">Ethnicity</label>
			{!! Form::select("students[$index][ethnicity_id]", $ethnicity_list, $student->ethnicity_id, [ 'class' => 'form-control' ] )  !!}
		</div>
		<div class="form-group">
			<label for="students[{{$index}}][grade]">Grade
            {!! Form::select("students[$index][grade]", [ "-", 5, 6, 7, 8, 9, 10, 11, 12, 13, 14 ], $student->grade, [ 'class' => 'form-control' ] )  !!}
			</label>
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{$index}}][email]">E-mail</label>
			<input type="text" class="form-control" id="students[{{$index}}][email]" name="students[{{$index}}][email]" placeholder="E-mail" value="{{ $student->email }}">
		</div>
		<div class="form-group">
		    <label class="sr-only" for="students[{{ $index }}][math_level_id]">Math Level</label>
		    {!! Form::select("students[$index][math_level_id]", $math_level::getList(), $student->math_level_id, [ 'class' => 'form-control' ] )  !!}
		</div>
		<div class="form-group">
			{!! Form::select("students[$index][tshirt]", [ 0 => '- Pick T-shirt Size -', 'XS' => 'XS - Extra Small', 'S' => 'S - Small', 'M' => 'M - Medium', 'L' => 'L - Large', 'XL' => 'XL - Extra Large', 'XXL' => 'XXL - Extra, Extra Large', '3XL' => '3XL - Triple Extra Large' ], $student->tshirt, [ 'class' => 'form-control' ] )  !!}
		</div>
	</div>
	<div class="col-md-1 text-center">
		<button type="button" class="btn btn-danger remove_student" aria-label="Remove Student"  data-index="{{$index}}" title="Remove Student">
  			<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
		</button>
	</div>
</div>

@if (array_key_exists('errors', $student))
	<div class="row student_{{ $index }}">
		<ul>
			@foreach($student['errors'] as $error)
				<li class="error">{{ $error }}</li>
			@endforeach
		</ul>
	</div>
@endif