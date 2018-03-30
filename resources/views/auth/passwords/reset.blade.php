@extends('layouts.mobile')

@section('main')
    <h1>Reset Password</h1>

    <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
        {{ csrf_field() }}
        <input type="hidden" name="token" value="{{ $token }}">
        <!-- username field -->
        <p>{!! Form::label('email', 'E-mail Address')  !!}</p>
        <p>{!! Form::text('email')  !!}</p>
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
    <!-- password field -->
        <p>{!! Form::label('password_confirmation', 'Confirm Password')  !!}</p>
        <p>{!! Form::password('password_confirmation')  !!}</p>
        @if ($errors->has('password_confirmation'))
            <p class="help-block">
                <strong>{{ $errors->first('password_confirmation') }}</strong>
            </p>
        @endif
        <p>{!! Form::submit('Reset Password')  !!}</p>
    </form>

@endsection
