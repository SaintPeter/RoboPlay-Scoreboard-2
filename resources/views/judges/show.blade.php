@extends('layouts.scaffold')

@section('main')

<h1>Show Judge</h1>

<p>{{ link_to_route('judges.index', 'Return to all judges') }}</p>

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
	</tbody>
</table>

@endsection
