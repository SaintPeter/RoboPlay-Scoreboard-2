@extends('layouts.scaffold')

@section('style')
<style>
    .indent {
    margin-left: 20px;
    }
</style>
@endsection

@section('main')
    {!! Form::open(['route' => 'store_users', 'class' => 'col-md-6']) !!}
    {{ csrf_field() }}
    <div class="form-group">
        <label for="details">First Name, Last Name, E-mail</label>
        <p>Seperate names/email with whitespace, one user per row.</p>
        {!! Form::textarea('details', null, [ 'class' => 'form-control', 'rows' => 10])  !!}
        <p>New User E-mails will be set to all members.</p>
        <em class="text-danger">Invoice Users should NOT be created using this form!</em>
    </div>

    <div class="form-group">
        {!! Form::label('',"Roles")  !!}
        <div class="indent">
            @foreach(UserTypes::$RoleList as $value => $display)
                <div class="checkbox">
                    <label>
                        {!! Form::hidden("roles[$value]", 0)  !!}
                        {!! Form::checkbox("roles[$value]", $value)  !!}
                        {{ $display }}
                    </label>
                </div>
            @endforeach
        </div>

    </div>

    <div class="form-group">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
        {!! link_to_route('list_users', 'Cancel', null, ['class' => 'btn btn-info']) !!}
    </div>


{!! Form::close() !!}

@if ($errors->any())
    <div class="col-md-4">
        <h3>Validation Errors</h3>
        <ul>
            {!! implode($errors->all('<li class="error">:message</li>')) !!}
        </ul>
    </div>
@endif
@endsection