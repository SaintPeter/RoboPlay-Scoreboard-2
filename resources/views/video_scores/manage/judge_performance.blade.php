@extends('layouts.scaffold')

@section('head')
	{{ HTML::script('js/jquery.TableCSVExport.js') }}
@endsection

@section('style')
.judges th {
	background-color: #428BCA;
	color: white;
	padding: 2px;
	text-align: center;
}

.judges th:first-child {
	text-align: left !important;
}

tr.score_row:nth-child(odd){
	background-color: #FAFAFA;
}

tr.score_row:nth-child(odd) td:last-child {
	background-color: #CCC !important;
}
tr.score_row:nth-child(even) td:last-child {
	background-color: #EEE !important;
}

.judges td {
	width: 70px;
	text-align: center;
	border: 1px solid lightgrey;
}

.judges tbody td:first-child {
	width: 200px !important;
	text-align: left !important;
	padding-left: 12px;
}
@endsection

@section('script')
<script>
	$(function() {
		$('#exportToCSV').click(function() {
			$('#judge_table').TableCSVExport( { delivery: 'download' });
		});
	});
</script>
@endsection


@section('main')
@include('partials.year_select')
@include('partials.scorenav', [ 'nav' => 'judges', 'year' => $year])

<table class="judges" id="judge_table">
	<thead>
		<th>Judge</th>
		<th>General</th>
		<th>Part</th>
		<th>Code</th>
		<th>Total</th>
	</thead>
	<tbody>
		@foreach($user_score_count as $user => $counts)
		<tr class="score_row">
			<td>{{ $user }}</td>
			<td>{{ $counts[1] }}</td>
			<td>{{ $counts[2] }}</td>
			<td>{{ $counts[3] }}</td>
			<td>{{ $counts['total'] }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
<br />
{!! Form::button('Export to CSV', [ 'class' => 'btn btn-success', 'id' => 'exportToCSV' ])  !!}
@endsection