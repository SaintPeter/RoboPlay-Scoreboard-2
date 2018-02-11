<form id="student_list">
<table class="table table-condensed">
	<tbody>
		@if(count($students) > 0)
			@foreach($students as $teacher_name => $students)
				<tr><td colspan="2"><strong>{{ $teacher_name }}</strong></td></tr>
				@foreach($students as $id => $student)
				<tr>
					<td class="text-center">{!! Form::checkbox('students[]', $id)  !!}</td>
					<td>{{ $student['name'] }} ({{  $student['year'] }})</td>
				</tr>
				@endforeach
			@endforeach
		@else
			<tr>
				<td>All Students are currently assigned.</td>
			</tr>
		@endif
	</tbody>
</table>
</form>