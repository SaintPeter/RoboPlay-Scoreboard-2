@extends('layouts.scaffold')

@section('style')
<style>
    .indent {
    margin-left: 20px;
    }
</style>
@endsection

@section('main')
    {!! Form::model($user, ['route' => ['update_user', $user], 'class' => 'col-md-6']) !!}
    {{ csrf_field() }}
    <div class="form-group">
        <label for="name">Name</label>
        {!! Form::text('name', null, [ 'class' => 'form-control'])  !!}
    </div>

    <div class="form-group">
        <label for="email">E-mail Address</label>
        {!! Form::text('email', null, [ 'class' => 'form-control'])  !!}
        <em>Note:  Changing e-mail address will trigger a password reset e-mail.</em>
    </div>

    <div class="form-group">
        {!! Form::label('',"Roles")  !!}
        <div class="indent">
            @foreach(UserTypes::$RoleList as $value => $display)
                <div class="checkbox">
                    <label>
                        {!! Form::hidden("roles[$value]", 0)  !!}
                        {!! Form::checkbox("roles[$value]", $value, $user->roles & $value)  !!}
                        {{ $display }}
                    </label>
                </div>
            @endforeach
        </div>

    </div>

    <div class="form-group">
        <label for="tshirt">T-Shirt Size</label>
        {!! Form::select('tshirt', $tshirt_sizes, old('tshirt'), [ 'id' => "tshirt", 'class' => 'form-control' ])  !!}
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