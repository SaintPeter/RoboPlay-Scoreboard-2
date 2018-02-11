@extends('layouts.scaffold')

@section('main')
{{ link_to_route('compyears.create', "Add Competition Year", null, [ 'class' => 'btn btn-primary btn-margin' ]) }}
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Year</th>
			<th>Competitions</th>
			<th>Divisions</th>
			<th>Video Competitions</th>
			<th>Video Divisions</th>
			<th>Invoice Type</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	@if(count($compyears) > 0)
		@foreach($compyears as $compyear)
		<tr>
			<td>{{ $compyear->year }}</td>
			<td>{!! join('<br />', $compyear->competitions()->pluck('name')->all()) !!}</td>
			<td>{!! join('<br />', $compyear->divisions()->pluck('name')->all()) !!}</td>
			<td>{!! join('<br />', $compyear->vid_competitions()->pluck('name')->all()) !!}</td>
			<td>{!! join('<br />', $compyear->vid_divisions()->pluck('name')->all()) !!}</td>
            <td>{{ $invoice_types[$compyear->invoice_type] }} <br>Id: {{  $compyear->invoice_type_id  }}</td>
			<td>
				{{ link_to_route('compyears.edit', 'Edit', array($compyear->id), array('class' => 'btn btn-info btn-margin')) }}
				{!! Form::open(array('method' => 'DELETE', 'route' => array('compyears.destroy', $compyear->id), 'style' => 'display: inline-block'))  !!}
				{!! Form::submit('Delete', array('class' => 'btn btn-danger btn-margin'))  !!}
				{!! Form::close()  !!}
			</td>
		</tr>
		@endforeach
	@else
		<tr><td colspan="6" class="text-center">No Competition Years</td></tr>
	@endif
	</tbody>
</table>
@endsection