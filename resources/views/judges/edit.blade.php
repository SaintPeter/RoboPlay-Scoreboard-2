@extends('layouts.scaffold')

@section('main')

<h1>Edit Judge</h1>
{!! Form::model($user, array('method' => 'PATCH', 'route' => array('judges.update', $user->id)))  !!}
	<ul>
        <li>
            {!! Form::label('username', 'Username:')  !!}
            {!! Form::text('username')  !!}
        </li>

        <li>
            {!! Form::label('wordpress_user_id', 'Wordpress_user_id:')  !!}
            {!! Form::input('number', 'wordpress_user_id')  !!}
        </li>

        <li>
            {!! Form::label('display_name', 'Display_name:')  !!}
            {!! Form::text('display_name')  !!}
        </li>

        <li>
            {!! Form::label('may_admin', 'May_admin:')  !!}
            {!! Form::checkbox('may_admin')  !!}
        </li>

		<li>
			{!! Form::submit('Update', array('class' => 'btn btn-info'))  !!}
			{{ link_to_route('judges.show', 'Cancel', $user->id, array('class' => 'btn')) }}
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
