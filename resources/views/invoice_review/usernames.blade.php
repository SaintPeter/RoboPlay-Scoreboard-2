@extends('layouts.scaffold')

@section('style')
<style>
    .video_section {
        display: none;
    }
    .video_notes {
        width: 100%;
    }
    .team_section {
        display: none;
    }
</style>
@endsection

@section('main')
<div class="pull-right">
	<ul class="nav nav-pills">
	    @foreach($comp_years as $comp_year)
			<li @if($comp_year->year == $year) class="active" @endif>{{ link_to_route('invoice_review', $comp_year->year, [ $comp_year->year ]  ) }}</li>
		@endforeach
	</ul>
</div>
{{ link_to_route('invoice_sync', "Sync with Wordpress", [ $year], [ 'class' => 'btn btn-info' ]  ) }}
&nbsp;&nbsp; Last Sync: {{ $last_sync }}
<table class="table">
<thead>
	<tr>
		<th>Invoice Number</th>
		<th>E-mail</th>
		<th>Name</th>
		<th>Username</th>
	</tr>
</thead>
<tbody>
@if(!empty($invoices))
	@foreach($invoices as $invoice)
	<tr>
		<td>
			{{ $invoice->remote_id }}
		</td>
		<td>
			{{ $invoice->wp_user->getName() }} &lt;{{ $invoice->wp_user->user_email }}&gt;
		</td>
		<td>
			{{ $invoice->wp_user->getName() }}
		</td>
		<td>
			{{ $invoice->wp_user->user_login }}
		</td>
	</tr>
	@endforeach
@else
	<tr><td>No Invoices</td></tr>
@endif
</tbody>
</table>
@endsection