@extends('layouts.scaffold')

@section('main')

<h1>All Score_runs</h1>

<p>{{ link_to_route('score_runs.create', 'Add new score_run') }}</p>

@if ($score_runs->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Run_number</th>
				<th>Run_time</th>
				<th>Scores</th>
				<th>Total</th>
				<th>user_id</th>
				<th>Team_id</th>
				<th>Challenge_id</th>
				<th>Division_id</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($score_runs as $score_run)
				<tr>
					<td>{{{ $score_run->run_number }}}</td>
					<td>{{{ $score_run->run_time }}}</td>
					<td>{{{ $score_run->scores }}}</td>
					<td>{{{ $score_run->total }}}</td>
					<td>{{{ $score_run->user_id }}}</td>
					<td>{{{ $score_run->team_id }}}</td>
					<td>{{{ $score_run->challenge_id }}}</td>
					<td>{{{ $score_run->division_id }}}</td>
                    <td>{{ link_to_route('score_runs.edit', 'Edit', array($score_run->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('score_runs.destroy', $score_run->id)))  !!}
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
                        {!! Form::close()  !!}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no score_runs
@endif

@endsection
