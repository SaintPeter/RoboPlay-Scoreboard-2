	<div class="form-group">
		{!! Form::label('student_form', 'Students:')  !!}
		<div class="form-inline" id="student_form">
			@if(Session::has('students') or count($students) > 0)
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
		{!! Form::button('Add Student', [ 'class' => 'btn btn-success btn-sm', 'id' => 'add_student', 'title' => 'Add Student' ])  !!}
		{!! Form::button('Mass Upload Students', [ 'class' => 'btn btn-success btn-sm', 'id' => 'mass_upload_students', 'title' => 'Upload Students' ])  !!}
		{!! Form::button('Choose Students', [ 'class' => 'btn btn-success btn-sm', 'id' => 'choose_students', 'title' => 'Choose Students' ] )  !!}
		<p><strong>Note:</strong>T-shirt sizes are only required if you have chosen the Complete Package </p>
	</div>

{{-- Update savedIndex with the number of students already displayed . . plus 1 --}}
<script>
	savedIndex = {{ $index + 1 }};
</script>
