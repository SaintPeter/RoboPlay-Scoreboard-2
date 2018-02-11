@extends('layouts.scaffold')

@section('main')

<h1>All Judges</h1>

<p>{{ link_to_route('judges.create', 'Add new judge') }}</p>

@if ($users->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Username</th>
				<th>Wordpress_user_id</th>
				<th>Display_name</th>
				<th>May_admin</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($users as $user)
				<tr>
					<td>{{{ $user->username }}}</td>
					<td>{{{ $user->wordpress_user_id }}}</td>
					<td>{{{ $user->name }}}</td>
					<td>{{{ $user->may_admin }}}</td>
                    <td>{{ link_to_route('judges.edit', 'Edit', array($user->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('judges.destroy', $user->id)))  !!}
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
                        {!! Form::close()  !!}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no judges
@endif

@endsection
