@extends('layouts.mobile')

@section('main')
<h1>Reset Password</h1>

@if (session('status'))
    <p class="alert alert-success">
        {{ session('status') }}
    </p>
@endif

<form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
    {{ csrf_field() }}

    <!-- username field -->
    <p>{!! Form::label('email', 'E-mail Address')  !!}</p>
    <p>{!! Form::text('email')  !!}</p>
    @if ($errors->has('email'))
        <p class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
        </p>
    @endif
    <p>{!! Form::submit('Send Password Reset Link')  !!}</p>
</form>
@endsection
