@extends('layouts.mobile')

@section('header', 'Roboplay Login')

@section('main')
{!! Form::open(array('url' => 'login', 'data-ajax' => 'false'))  !!}
	{{ csrf_field() }}
	<!-- check for login errors flash var -->
	@if ($errors->has('email'))
		<span class="help-block">
			<strong>{{ $errors->first('email') }}</strong>
		</span>
	@endif

	@if (Session::has('login_errors'))
		<span class="error">Username or password incorrect.</span>
	@endif

	<!-- username field -->
	<p>{!! Form::label('username', 'Wordpress Username (Not your e-mail address)')  !!}</p>
	<p>{!! Form::text('username')  !!}</p>
	<!-- password field -->
	<p>{!! Form::label('password', 'Password')  !!}</p>
	<p>{!! Form::password('password')  !!}</p>
	<!-- submit button -->
	<p>{!! Form::submit('Login')  !!}</p>
{!! Form::close()  !!}
@endsection
