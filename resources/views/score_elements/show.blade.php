@extends('layouts.scaffold')

@section('main')

<h1>Show Score_element</h1>

<p>{{ link_to_route('score_elements.index', 'Return to all score_elements') }}</p>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
				<th>Display_text</th>
				<th>Element_number</th>
				<th>Base_value</th>
				<th>Multiplier</th>
				<th>Min_entry</th>
				<th>Max_entry</th>
				<th>Type</th>
				<th>Challenge_id</th>
		</tr>
	</thead>

	<tbody>
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
	</tbody>
</table>

@endsection
