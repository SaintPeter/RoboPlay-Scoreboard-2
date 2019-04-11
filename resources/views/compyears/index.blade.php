@extends('layouts.scaffold')

@section('script')
{{ HTML::style('https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.0.3/jquery-confirm.min.css') }}
{{ HTML::script('//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.0.3/jquery-confirm.min.js') }}

<script>
    $(document).ready(function() {
        $('.delete_scores').confirm({
            title: "Clear Scores?",
            content: "This will premanently clear these scores.",
            buttons: {
                delete: function() {
                    location.href = this.$target.attr('href');
                },
                cancel: function() {

                }
            }
        });
    });
</script>
@endsection

@section('main')
{{ link_to_route('compyears.create', "Add Competition Year", null, [ 'class' => 'btn btn-primary btn-margin' ]) }}
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Year</th>
			<th>Competitions</th>
			<th>Video Competitions</th>
			<th>Invoice Type</th>
            <th>Dates</th>
			<th>Actions</th>
		</tr>
	</thead>
	<tbody>
	@if(count($compyears) > 0)
		@foreach($compyears as $compyear)
		<tr>
			<td>{{ $compyear->year }}</td>
            <td>
                @foreach($compyear->competitions as $comp)
                    <strong>
                        {!! $comp->name !!}
                    </strong>
                    <ul>
                    @foreach($comp->divisions as $div)
                        <li>{!! $div->name !!}</li>
                    @endforeach
                    </ul>

                @endforeach
            </td>
            <td>
                @foreach($compyear->vid_competitions as $vid_comp)
                    <strong>
                        {!! $vid_comp->name !!}
                    </strong>
                    <ul>
                        @foreach($vid_comp->divisions as $div)
                            <li>{!! $div->name !!}</li>
                        @endforeach
                    </ul>
                @endforeach
            </td>
            <td>{{ $invoice_types[$compyear->invoice_type] }} <br>Id: {{  $compyear->invoice_type_id  }}</td>
            <td>
                <strong>Reminders:</strong><br>
                {!! $compyear->reminder_start->format('M&\n\b\s\p;j') !!}&nbsp;-&nbsp;{!! $compyear->reminder_end->format('M&\n\b\s\p;j') !!} <br>
                <strong>Last Edit:</strong><br>
                {!! $compyear->edit_end->format('M&\n\b\s\p;j') !!}
            </td>
			<td>
				{{ link_to_route('compyears.edit', 'Edit', array($compyear->id), array('class' => 'btn btn-info btn-margin')) }}
				{!! Form::open(array('method' => 'DELETE', 'route' => array('compyears.destroy', $compyear->id), 'style' => 'display: inline-block'))  !!}
				{!! Form::submit('Delete', array('class' => 'btn btn-danger btn-margin'))  !!}
				{!! Form::close()  !!}
                {{ link_to_route('compyears.clear_div_scores', 'Clear Division Scores', [$compyear->id], [ 'class' => 'btn btn-warning btn-margin delete_scores']) }}
			</td>
		</tr>
		@endforeach
	@else
		<tr><td colspan="6" class="text-center">No Competition Years</td></tr>
	@endif
	</tbody>
</table>
@endsection