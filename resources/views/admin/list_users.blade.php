@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.filtertable.min.js') }}
    {{ HTML::script('js/jquery.tablesorter.min.js') }}
    {{ HTML::style('css/tablesorter.css') }}
@endsection

@section('script')
<script>
	$(function() {
		$("#user_table").filterTable();
        $( "#user_table" ).tablesorter({textExtraction: myTextExtraction});
	});

    var myTextExtraction = function(node)
    {
        // extract data from markup and return it
        return (node.innerHTML=='-') ? -1 : node.innerHTML ;
    }

</script>
@endsection

@inject('userTypes',"App\Enums\UserTypes")

@section('main')
{{ link_to_route('create_user',"Add User", null,[ 'class' => 'btn btn-primary pull-right btn-margin' ]) }}
{{ link_to_route('create_users',"Mass Add Users", null,[ 'class' => 'btn btn-info pull-right btn-margin' ]) }}
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
			<td>
                <a href="{{ route('edit_user', [ $user->id ])}}" class="btn btn-sm btn-success" title="Edit User">
                    <i class="fa fa-edit"></i>
                </a>
                <a href="{{ route('admin_reset_password', [ $user->id ])}}" class="btn btn-sm btn-info" title="Send Password Reset E-mail">
                    <i class="fa fa-envelope"></i>
                </a>
                <a href="{{ route('switch_user', [ $user->id ])}}" class="btn btn-sm btn-primary" title="Switch to User">
                    <i class="fa fa-sign-out"></i>
                </a>
            </td>
		</tr>
		@endforeach
	</tbody>
</table>
@endsection