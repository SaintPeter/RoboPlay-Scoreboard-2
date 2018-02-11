@extends('layouts.scaffold')

@section('style')
tr.done td{
	background-color: SkyBlue !important;
}
tr.done2 td{
	background-color: LightBlue !important;
}

td.bg-success {
	background-color: #DFF0D8 !important;
}
td.bg-warning {
	background-color: #FCF8E3 !important;
}

@endsection

@section('script')
<script>
	$(function() {
		$('.check_paid').change(function(e){
			var value = $(this).is(':checked') ? 1 : 0;
			var invoice_no = $(this).attr('invoice_no');
			$.get('ajax/set_paid/' + invoice_no + '/' + value, function(data) {
				$('#invoice_row_' + invoice_no).addClass('done');
			});
		});

		$('.select_div').change(function(e) {
			var value = $(this).val();
			var invoice_no = $(this).attr('invoice_no');
			$.get('ajax/set_div/' + invoice_no + '/' + value, function(data) {
				$('#invoice_row_' + invoice_no).addClass('done2');
			});
		});

		$('.select_vid_div').change(function(e) {
			var value = $(this).val();
			var invoice_no = $(this).attr('invoice_no');
			$.get('ajax/set_vid_div/' + invoice_no + '/' + value, function(data) {
				$('#invoice_row_' + invoice_no).addClass('done2');
			});
		});
	});
</script>
@endsection

@section('main')
<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Invoice #</th>
				<th>Name</th>
				<th>M</th>
				<th>County/District/School</th>
				<th>Teams</th>
				<th>Vid Teams</th>
				<th>Div</th>
				<th>Division / Video Division</th>
				<th>Total</th>
				<th>Paid</th>
			</tr>
		</thead>
		<tbody>
			@foreach($invoices as $invoice)
			<tr id="invoice_row_{{ $invoice->invoice_no }}">
				<td>{{ $invoice->invoice_no }}</td>
				@if( $invoice->user->metadata['is_teacher'])
					<td class="bg-success">
						{{ $invoice->user->metadata['first_name'] }} {{ $invoice->user->metadata['last_name'] }} <br />
						{{ $invoice->user->user_login }}<br />
						Is a Teacher
					</td>
				@else
					<td class="bg-warning">
						{{ $invoice->user->metadata['first_name'] }} {{ $invoice->user->metadata['last_name'] }} <br />
						{{ $invoice->user->user_login }}
						<a href="/wp-admin/users.php?page=users-user-role-editor.php&object=user&user_id={{ $invoice->user->ID }}">Edit</a> <br />
							Not a Teacher
					</td>
				@endif
				<td><a href="mailto:{{ $invoice->user->user_email }}" class="btn btn-info btn-mini"><i class=icon-envelope>M</i> </a></td>
				@if(isset($invoice->school))
					<td>
						<strong>C:</strong> {{ $invoice->school->county }}<br />
						<strong>D:</strong> {{ $invoice->school->district }}<br />
						<strong>S:</strong> {{ $invoice->school->name }}<br />
					</td>
				@else
					<td>Not Set</td>
				@endif
				<td>{{ $invoice->team_count }}</td>
				<td>{{ $invoice->video_count }}</td>
				<td>{{ $invoice->division }}</td>
				<td>{!! Form::select('division_id', $divisions, $invoice->division_id ,[ 'class' => 'select_div', 'invoice_no' => $invoice->invoice_no ])  !!}<br /><br />
				{!! Form::select('vid_division_id', $vid_divisions, $invoice->vid_division_id ,[ 'class' => 'select_vid_div', 'invoice_no' => $invoice->invoice_no ])  !!}</td>
				<td>${{ $invoice->total }}</td>
				<td><input type="checkbox" class="check_paid" invoice_no="{{ $invoice->invoice_no }}" value="1" @if($invoice->paid)checked="checked">@endif</td>
			</tr>
			@endforeach
			<tr>
				<td colspan="4" style="text-align: right;">Totals</td>
				<td>{{ $invoices->sum('team_count') }}</td>
				<td>{{ $invoices->sum('video_count') }}</td>
				<td colspan="4" >&nbsp;</td>
				</tr>
		</tbody>
@endsection