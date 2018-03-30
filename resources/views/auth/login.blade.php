@extends('layouts.mobile')

@section('header', 'Roboplay Login')

@section('main')
    {!! Form::open(['url' => 'login', 'data-ajax' => 'false'])  !!}
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
    <p>{!! Form::label('email', 'E-mail Address')  !!}</p>
    <p>{!! Form::text('email', old('email'))  !!}</p>
    @if ($errors->has('email'))
        <p class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
        </p>
    @endif
    <!-- password field -->
    <p>{!! Form::label('password', 'Password')  !!}</p>
    <p>{!! Form::password('password')  !!}</p>
    @if ($errors->has('password'))
        <p class="help-block">
            <strong>{{ $errors->first('password') }}</strong>
        </p>
    @endif
    <!-- submit button -->
    <p>{!! Form::submit('Login')  !!}</p>
    <label>
        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
    </label>
    <p>
        <a class="btn btn-link" href="{{ route('password.request') }}">
            Forgot Your Password?
        </a>
    </p>
    {!! Form::close()  !!}
@endsection
