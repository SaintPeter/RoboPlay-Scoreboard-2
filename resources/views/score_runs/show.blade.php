@extends('layouts.scaffold')

@section('main')

<h1>Show Score_run</h1>

<p>{{ link_to_route('score_runs.index', 'Return to all score_runs') }}</p>

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
	</tbody>
</table>

@endsection
