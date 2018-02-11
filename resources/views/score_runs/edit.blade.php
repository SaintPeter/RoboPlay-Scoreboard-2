@extends('layouts.scaffold')

@section('main')

<h1>Edit Score_run</h1>
{!! Form::model($score_run, array('method' => 'PATCH', 'route' => array('score_runs.update', $score_run->id)))  !!}
	<ul>
        <li>
            {!! Form::label('run_number', 'Run_number:')  !!}
            {!! Form::input('number', 'run_number')  !!}
        </li>

        <li>
            {!! Form::label('run_time', 'Run_time:')  !!}
            {!! Form::text('run_time')  !!}
        </li>

        <li>
            {!! Form::label('scores', 'Scores:')  !!}
            {!! Form::text('scores')  !!}
        </li>

        <li>
            {!! Form::label('total', 'Total:')  !!}
            {!! Form::input('number', 'total')  !!}
        </li>

        <li>
            {!! Form::label('user_id', 'user_id:')  !!}
            {!! Form::input('number', 'user_id')  !!}
        </li>

        <li>
            {!! Form::label('team_id', 'Team_id:')  !!}
            {!! Form::input('number', 'team_id')  !!}
        </li>

        <li>
            {!! Form::label('challenge_id', 'Challenge_id:')  !!}
            {!! Form::input('number', 'challenge_id')  !!}
        </li>

        <li>
            {!! Form::label('division_id', 'Division_id:')  !!}
            {!! Form::input('number', 'division_id')  !!}
        </li>

		<li>
			{!! Form::submit('Update', array('class' => 'btn btn-info'))  !!}
			{{ link_to_route('score_runs.show', 'Cancel', $score_run->id, array('class' => 'btn')) }}
		</li>
	</ul>
{!! Form::close()  !!}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

@endsection
