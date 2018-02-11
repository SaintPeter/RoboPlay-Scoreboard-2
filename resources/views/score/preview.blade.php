@extends('layouts.mobile')

@section('header', 'Score')

@section('style')
	.ui-li-static {
		white-space: normal; !important
	}
@endsection

@section('main')
<p>
	<strong>Division: </strong>{{{ $team->division->name }}}<br />
	<strong>Team: </strong>{{{ $team->name }}}
</p>
<h2>Run {{{ $run_number }}}</h2>
<p>
	<strong>{{{ $challenge->display_name }}}</strong><br />
	{{{ $challenge->rules }}}
</p>
{!! Form::open(array('route' => array('score.save', $team->id, $challenge->id), 'id' => 'se_form'))  !!}
	<ul data-role="listview">
		@foreach($scores as $name => $score)
			<li>{{ $name }}: {{ $score }} {!! Form::hidden('scores[]', $score)  !!}</li>
		@endforeach
		<li>
			Total: {{ $total }} {!! Form::hidden('total', $total)  !!}
		</li>
		<li>
			<fieldset class="ui-grid-a">
				<div class="ui-block-a">{!! Form::submit('Save', array('class' => 'ui-btn', 'name' => 'submit'))  !!}</div>
				<div class="ui-block-b">{!! Form::submit('Edit', array('class' => 'ui-btn', 'name' => 'cancel'))  !!}</div>
			</fieldset>
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