@extends('layouts.scaffold')

@section('script')
<script>
    $(document).ready(function(){
       $('#send_password').click(function(e){
           var value = $(this).is(':checked');
           if(value) {
               $('.passwords').hide();
           } else {
               $('.passwords').show();
           }
       }); 
    });
</script>    
@endsection

@section('style')
    .indent {
        margin-left: 20px;
    }

@endsection

@section('main')
{!! Form::open(['route' => 'store_user', 'class' => 'col-md-6']) !!}
    {{ csrf_field() }}
    <div class="form-group">
        <label for="name">Name</label>
        {!! Form::text('name', null, [ 'class' => 'form-control'])  !!}
    </div>

    <div class="form-group">
        <label for="email">E-mail Address</label>
        {!! Form::text('email', null, [ 'class' => 'form-control'])  !!}
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
        <label for="tshirt">T-Shirt Size</label>
        {!! Form::select('tshirt', $tshirt_sizes, old('tshirt'), [ 'id' => "tshirt", 'class' => 'form-control' ])  !!}
    </div>

    <div class="form-group">
        <label>Password or Reset</label>
        <div class="checkbox">
            <label>
                {!! Form::hidden("send_password", 0)  !!}
                {!! Form::checkbox("send_password", null, 0, ['id' => 'send_password'])  !!}
                Send Password Reset instead of setting password
            </label>
        </div>
    </div>

    <div class="form-group passwords">
        <label for="password">Password</label>
        {!! Form::password('password', ['class' => 'form-control']) !!}

        <label for="password">Password (Confirm)</label>
        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
        {!! link_to_route('list_users', 'Cancel', null, ['class' => 'btn btn-info']) !!}
    </div>


{!! Form::close() !!}
@endsection