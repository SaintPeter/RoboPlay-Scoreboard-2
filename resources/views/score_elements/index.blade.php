@extends('layouts.scaffold')

@section('main')

<h1>All Score_elements</h1>

<p>{{ link_to_route('score_elements.create', 'Add new score_element') }}</p>

@if ($score_elements->count())
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th>Name</th>
				<th>Display Text</th>
				<th>Element Number</th>
				<th>Base Value</th>
				<th>Multiplier</th>
				<th>Min Entry</th>
				<th>Max Entry</th>
				<th>Type</th>
				<th>Challenge_id</th>
			</tr>
		</thead>

		<tbody>
			@foreach ($score_elements as $score_element)
				<tr>
					<td>{{{ $score_element->name }}}</td>
					<td>{{ $score_element->display_text }}</td>
					<td>{{{ $score_element->element_number }}}</td>
					<td>{{{ $score_element->base_value }}}</td>
					<td>{{{ $score_element->multiplier }}}</td>
					<td>{{{ $score_element->min_entry }}}</td>
					<td>{{{ $score_element->max_entry }}}</td>
					<td>{{{ $score_element->type }}}</td>
					<td>{{{ $score_element->challenge_id }}}</td>
                    <td>{{ link_to_route('score_elements.edit', 'Edit', array($score_element->id), array('class' => 'btn btn-info')) }}</td>
                    <td>
                        {!! Form::open(array('method' => 'DELETE', 'route' => array('score_elements.destroy', $score_element->id)))  !!}
                            {!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
                        {!! Form::close()  !!}
                    </td>
				</tr>
			@endforeach
		</tbody>
	</table>
@else
	There are no score_elements
@endif

@endsection
