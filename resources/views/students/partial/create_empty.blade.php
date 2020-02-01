<!-- student.partial.create_empty -->
@inject('math_level', "App\Models\Math_Level")
<div class="vertical-container row student_{{$index}}">
	@if($index > 0)
		<hr>
	@endif
	<div class="col-md-12">
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][first_name]">First Name</label>
			<input type="text" class="form-control" id="students[{{ $index }}][first_name]" name="students[{{ $index }}][first_name]" placeholder="First Name">
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][first_name]">Middle/Nick Name</label>
			<input type="text" class="form-control" id="students[{{ $index }}][middle_name]" name="students[{{ $index }}][middle_name]" placeholder="Middle/Nick">
		</div>
		<div class="checkbox">
		    <label>
		      <input type="hidden" name="students[{{ $index }}][nickname]" value="0">
		      <input type="checkbox" name="students[{{ $index }}][nickname]" value="1"> Is Nickname
		    </label>
		  </div>
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][last_name]">Last Name</label>
			<input type="text" class="form-control" id="students[{{ $index }}][last_name]" name="students[{{ $index }}][last_name]" placeholder="Last Name">
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][gender]">Gender</label>
			{!! Form::select("students[$index][gender]", [ 0 => '- Pick Gender -', 'Male' => 'Male', 'Female' => 'Female' ], null, [ 'class' => 'form-control' ] )  !!}
		</div>
		<div class="form-group">
			<label class="sr-only" for="students[{{ $index }}][ethnicity_id]">Ethnicity</label>
			{!! Form::select("students[$index][ethnicity_id]", $ethnicity_list, null, [ 'class' => 'form-control' ] )  !!}
		</div>
		<div class="form-group">
			<label for="students[{{ $index }}][grade]">Grade
			{!! Form::select("students[$index][grade]", [ 0 => "-", 5 => 5, 6 => 6, 7=> 7,8=> 8, 9=> 9, 10 => 10, 11 => 11, 12 => 12, 13 => 13, 14=> 14 ], null, [ 'class' => 'form-control' ] )  !!}</label>
		</div>
		<div class="form-group">
		    <label class="sr-only" for="students[{{ $index }}][math_level_id]">Math Level</label>
		    {!! Form::select("students[$index][math_level_id]", $math_level::getList(), null, [ 'class' => 'form-control' ] )  !!}
		</div>
		<div class="form-group">
			{!! Form::select("students[$index][tshirt]", [ 0 => '- Pick T-shirt Size -',
			      'YM' => 'YM - Youth Medium',
			      'YL' => 'YL - Youth Large',
			      'YXL' => 'YXL - Youth Extra Large',
			      'S' => 'S - Small',
			      'M' => 'M - Medium',
			      'L' => 'L - Large',
			      'XL' => 'XL - Extra Large',
			      'XXL' => 'XXL - Extra, Extra Large',
			      '3XL' => '3XL - Triple Extra Large'
			      ], null, [ 'class' => 'form-control' ] )  !!}
		</div>
	</div>
	<div class="col-md-1 text-center">
		<button type="button" class="btn btn-danger remove_student" aria-label="Remove Student" data-index="{{$index}}" title="Remove Student">
  			<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
		</button>
	</div>
</div>
