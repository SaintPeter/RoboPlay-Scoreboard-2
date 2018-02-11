@extends('layouts.scaffold')

@section('main')

<h1>Create Score_run</h1>

{!! Form::open(array('route' => 'score_runs.store'))  !!}
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
			{!! Form::submit('Submit', array('class' => 'btn btn-info'))  !!}
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


