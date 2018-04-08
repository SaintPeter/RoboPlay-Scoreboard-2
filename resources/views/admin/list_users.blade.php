@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.filtertable.min.js') }}
@endsection

@section('script')
<script>
	$(function() {
		$("#user_table").filterTable();
	});
</script>
@endsection

@inject('userTypes',"App\Enums\UserTypes")

@section('main')
<table class="table table-striped table-bordered" id="user_table">
	<thead>
		<tr>
			<th>Name</th>
			<th>E-mail</th>
            <th>Password</th>
			<th>User Id</th>
			<th>Roles</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
		@foreach($users as $user)
		<tr>
			<td>{{ $user->name }}</td>
			<td>{{ $user->email }}</td>
            <td>
                @if($user->password)
                    @if($user->password_resets)
                        <span class="text-info">Set, Reset on <br>{{ $user->password_resets->created_at }}</span>
                    @else
                        <span class="text-success">Set</span>
                    @endif
                @else
                    @if($user->password_resets)
                        <span class="text-info">Not Set, Reset on <br>{{ $user->password_resets->created_at }}</span>
                    @else
                        <span class="text-danger">Not Set</span>
                    @endif
                @endif

            </td>
			<td>{{ $user->id }}</td>
			<td>{{ join(',',$userTypes::getAllDescriptions($user->roles)) }}</td>
			<td>{{ link_to_route('switch_user', 'Switch To', [ $user->id ], [ 'class' => 'btn btn-primary' ]) }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@endsection