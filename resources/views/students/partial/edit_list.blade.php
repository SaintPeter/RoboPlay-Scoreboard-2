@if(!empty($students))
	@foreach($students as $student)
		@if(is_object($student))
			@include('students.partial.edit',	compact('index', 'ethnicity_list', 'student' ))
		@else
			@include('students.partial.create',	compact('index', 'ethnicity_list', 'student' ))
		@endif
		<?php $index++; ?>
	@endforeach
	<script>savedIndex += {{ $index + count($students) + 1 }};</script>
@endif