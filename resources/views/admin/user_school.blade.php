@extends('layouts.scaffold')

@section('head')
{{ HTML::script('js/jquery.jCombo.js') }}
@endsection

@section('style')
tr.highlighted td{
	background-color: Yellow !important;
}
tr.done td{
	background-color: SkyBlue !important;
}
html, body { position: relative }
@endsection

@section('script')
<script>
	var current_user = 0;
	 $(function() {
	 $( "#choose_school" ).hide();
		$( "#choose_school" ).clone().attr("id","choose_school_active").dialog({ autoOpen: false, width: 500, close: closed_dialog });
		$(".set_school").click(function(e) {
			current_user = $(e.target).attr('user_id');
			$("#row_id_" + current_user).addClass('highlighted');
			$( "#choose_school_active" ).dialog("open");
			$( "#choose_school_active #select_county" ).jCombo({url: "ajax/c"});
			$( "#choose_school_active #select_district" ).jCombo({url: "ajax/d/",
																  parent: "#choose_school_active #select_county",
																  initial_text: "-- Select District --",
																  });
			$( "#choose_school_active #select_school" ).jCombo({url: "ajax/s/",
																  parent: "#choose_school_active #select_district",
																  initial_text: "-- Select School --",
																  });
			$( "#choose_school_active #save_school" ).click(function(e) {
					e.preventDefault();
					$("#choose_school_active #user_id").val(current_user);
					var data = $( "#choose_school_active #select_table" ).serialize();
					$.post( 'ajax/save_school', data, function(d) {
						$('#school_id_' + current_user).html($("#choose_school_active #select_school").val());
						$('#choose_school_active').dialog('close').remove();
						$( "#choose_school" ).clone().attr("id","choose_school_active").dialog({ autoOpen: false, width: 500, close: closed_dialog });
						$("#row_id_" + current_user).removeClass('highlighted');
						$("#row_id_" + current_user).addClass('done');
					 });
				});
		});
	});

	function closed_dialog() {
		$("#row_id_" + current_user).removeClass('highlighted');
	}
</script>
@endsection

@section('main')
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>County</th>
			<th>Division</th>
			<th>School Name</th>
			<th>School Id</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	@foreach($invoices as $invoice)
		<tr id="row_id_{{ $invoice->user->ID }}">
			<td>{{ $invoice->user->getMeta('first_name') }} {{ $invoice->user->getMeta('last_name') }}</td>
			<td>{{ Schools::with('district', 'district.county')->find($invoice->user->getMeta('wp_school_id'))->district->county->name }}</td>
			<td>{{ Schools::with('district')->find($invoice->user->getMeta('wp_school_id'))->district->name }}</td>
			<td>{{ Schools::find($invoice->user->getMeta('wp_school_id'))->name }}</td>
			<td id="school_id_{{ $invoice->user->ID }}">{{ $invoice->user->getMeta('wp_school_id') }}</td>
			<td><button user_id="{{ $invoice->user->ID }}" class="btn btn-primary set_school">Set School</button></td>
		</tr>
	@endforeach
	</tbody>
</table>

<div id="choose_school" title="Choose School">
	<form id="select_table">
	<table>
		<tr>
			<td>County</td>
			<td><select name="select_county" id="select_county"></select></td>
		</tr>
		<tr>
			<td>District</td>
			<td><select name="select_district" id="select_district"></select></td>
		</tr>
		<tr>
			<td>School</td>
			<td><select name="select_school" id="select_school"></select></td>
		</tr>
		<tr>
			<td colspan="2">
				<button id="save_school" class="btn btn-primary">Save School</button>
				<input name="user_id" id="user_id" type="hidden" value="">
			</td>
		</tr>
	</table>
	</form>
</div>

@endsection