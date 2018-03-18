@extends('layouts.scaffold')

@section('script')
<script>
var WIDTH_NARROW = 400;
var WIDTH_WIDE = 700;
$(function() {
	$( "#dialog" ).clone().attr('id', 'active_dialog').dialog({ autoOpen: false, minWidth: WIDTH_NARROW });
	$( "#random_dialog" ).clone().attr('id', 'active_random_dialog').dialog({ autoOpen: false });

	$(document).on('click', '.random_close', dialog_close_handler );
    $(document).on('click', '.add_row', score_map_add_row );
    $(document).on('click', '.delete_row', score_map_delete_row );

	// Add Score Element Button
	$("#add_score_element").click(function() {
		$.get("{{ route('score_elements.create', $challenge->id) }}",
			function( data ) {
				$( "#active_dialog" ).html(data);
				$( "#active_dialog" ).dialog("open");
			}, "html" ).done(setup_form_handler);
	});

	// Edit Score Element Button Functions
	$(".btn_se_edit").click( function (event) {
		event.preventDefault();
		$.get( $(this).attr('href'),
			function( data ) {
			$( "#active_dialog" ).html( data );
			$( "#active_dialog" ).dialog("open");
			}, "html" ).done(setup_form_handler);
	});


	// Add Random Element Button
	$("#add_random_element").click(function() {
		$.get("{{ route('randoms.create', $challenge->id) }}",
			function( data ) {
			    $( "#active_random_dialog" ).dialog('option', 'title', 'Create Random');
				$( "#active_random_dialog" ).html(data);
				$( "#active_random_dialog" ).dialog("open");
			}, "html" ).done(setup_random_handler);
	});

	// Add Random Element Button
	$("#add_random_list").click(function() {
		$.get("{{ route('random_list.create', $challenge->id) }}",
			function( data ) {
			    $( "#active_random_dialog" ).dialog('option', 'title', 'Create Random List');
				$( "#active_random_dialog" ).html(data);
				$( "#active_random_dialog" ).dialog("open");
			}, "html" ).done(setup_random_list_handler);
	});

	// Edit Random Element Button Functions
	$(".btn_random_edit").click( function (event) {
		event.preventDefault();
		$.get( $(this).attr('href'),
			function( data ) {
			$( "#active_random_dialog" ).dialog('option', 'title', 'Edit Random');
			$( "#active_random_dialog" ).html( data );
			$( "#active_random_dialog" ).dialog("open");
			}, "html" ).done(setup_random_handler);
	});

	$(".btn_random_list_edit").click( function (event) {
		event.preventDefault();
		$.get( $(this).attr('href'),
			function( data ) {
			$( "#active_random_dialog" ).dialog('option', 'title', 'Edit Random List');
			$( "#active_random_dialog" ).html( data );
			$( "#active_random_dialog" ).dialog("open");
			}, "html" ).done(setup_random_list_handler);
	});
	$(".btn_random_elements_edit").click( function (event) {
		event.preventDefault();
		$.get( $(this).attr('href'),
			function( data ) {
			$( "#active_random_dialog" ).dialog('option', 'title', 'Edit Random Elements List');
			$( "#active_random_dialog" ).html( data );
			$( "#active_random_dialog" ).dialog("open");
			}, "html" ).done(setup_elements_list_handler);
	});
	$(".btn_random_elements_show").click( function (event) {
		event.preventDefault();
		$.get( $(this).attr('href'),
			function( data ) {
			$( "#active_random_dialog" ).dialog('option', 'title', 'Show Random List');
			$( "#active_random_dialog" ).html( data );
			$( "#active_random_dialog" ).dialog("open");
			}, "html" ).done(setup_random_list_handler);
	});
});

function setup_form_handler() {
	jQuery( ".numeric" ).spinner();
	jQuery( "#se_form" ).on( "submit", function( event ) {
		event.preventDefault();
		jQuery.post( jQuery(this).attr('action'), jQuery( this ).serialize(), function(data) {
			if(data == "true") {
				location.reload(true);
			} else {
				jQuery("#active_dialog").html(data);
				setup_form_handler();
			}
		}, "html" );
	});
	jQuery( ".dialog_close" ).on('click', function (event) {
    	event.preventDefault();
    	jQuery( "#active_dialog" ).dialog("close");
    	jQuery( "#active_dialog" ).remove();
    	$( "#dialog" ).clone().attr('id', 'active_dialog').dialog({ autoOpen: false });
    });

	// Setup Dialog Width
	var hasScoreMap = $("input[name|='has_score_map']");
    var dialog = $("#active_dialog");
    if(hasScoreMap.val() == 1) {
        dialog.dialog("option", "minWidth", WIDTH_WIDE );
        dialog.dialog("option", "width", WIDTH_WIDE );
    } else {
        dialog.dialog("option", "minWidth", WIDTH_NARROW );
        dialog.dialog("option", "width", WIDTH_NARROW );
    }
    $("#edit_map").click(edit_map_handler);
}

function edit_map_handler() {
    var button = $(this);
    var dialog = $("#active_dialog");
    var hasScoreMap = $("input[name|='has_score_map']");
    var minWidth = dialog.dialog("option", "minWidth");

    $("#maincol").toggleClass('col-lg-12').toggleClass('col-lg-8');
    $("#mapcol").toggleClass('hidden');

    if(minWidth == WIDTH_NARROW) {
        dialog.dialog("option", "minWidth", WIDTH_WIDE );
        dialog.dialog("option", "width", WIDTH_WIDE );
        hasScoreMap.val(1);
        button.attr('value','Remove Map');
    } else {
        dialog.dialog("option", "minWidth", WIDTH_NARROW );
        dialog.dialog("option", "width", WIDTH_NARROW );
        hasScoreMap.val(0);
        button.attr('value','Create Map');
    }
}

function score_map_add_row(e) {
    var button = $(this);
    var index = button.data('index');

    var newRow = $(
        '<tr id="score_map_row_' + index + '">' +
        '<td><input type="text" name="score_map[' + index + '][i]" class="numeric text-center" /></td>' +
        '<td><input type="text" name="score_map[' + index + '][v]" class="numeric text-center" /></td>' +
        '<td><a href="javascript:void(0)" class="btn btn-xs delete_row" data-index="'+ index +'" title="Delete Row">' +
        '<span class="glyphicon glyphicon-minus text-danger" aria-hidden="true"></span>' +
        '</a></td>' +
        '</tr>');

    newRow.find('.numeric').spinner();

    $('.score_map tbody').append(newRow);

    // Increment the next row
    button.data('index', index + 1);
}

function score_map_delete_row(e) {
    var button = $(this);
    var index = button.data('index');

    $('#score_map_row_' + index).remove();

    // Decrement the next row
    button.data('index', index - 1);
}

function setup_random_handler() {
	jQuery( ".numeric" ).spinner();
	jQuery( "#random_form" ).on( "submit", function( event ) {
		event.preventDefault();
		jQuery.post( jQuery(this).attr('action'), jQuery( this ).serialize(), function(data) {
			if(data == "true") {
				location.reload(true);
			} else {
				jQuery("#active_random_dialog").html(data);
				setup_random_handler();
			}
		}, "html" );
	});
}

function setup_random_list_handler() {
	jQuery( ".numeric" ).spinner();
	jQuery( "#random_list_form" ).on( "submit", function( event ) {
		event.preventDefault();
		jQuery.post( jQuery(this).attr('action'), jQuery( this ).serialize(), function(data) {
			if(data == "true") {
				location.reload(true);
			} else {
				jQuery("#active_random_dialog").html(data);
				setup_random_list_handler();
			}
		}, "html" );
	});
}

function setup_elements_list_handler() {
	jQuery( "#random_list_form" ).on( "submit", function( event ) {
		event.preventDefault();
		jQuery.post( jQuery(this).attr('action'), jQuery( this ).serialize(), function(data) {
			if(data == "true") {
				location.reload(true);
			} else {
				jQuery("#active_random_dialog").html(data);
				setup_elements_list_handler();
			}
		}, "html" );
	});
}

function dialog_close_handler(event) {
	event.preventDefault();
	jQuery( "#active_random_dialog" ).dialog("close");
	jQuery( "#active_random_dialog" ).remove();
	$( "#dialog" ).clone().attr('id', 'active_random_dialog').dialog({ autoOpen: false });
}
</script>
@endsection

@section('head')
    <style>
        .mix .col-md-6 {
            margin-left: 0px;
        }
        button:focus {outline:0;}

        .score_map input {
            width: 50px;
        }

    </style>
@endsection

@inject('randoms', "App\Models\Random")

@section('main')
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Internal Name</th>
				<th>Display Name</th>
				<th>Rules</th>
				<th>Points</th>
				<th>Level</th>
                <th>Year</th>
				<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td>{{{ $challenge->internal_name }}}</td>
					<td>{{ $challenge->display_name }}</td>
					<td>{!! $challenge->rules !!} </td>
					<td>{{{ $challenge->points }}}</td>
					<td>{{{ $challenge->level }}}</td>
                    <td>{{{ $challenge->year }}}</td>
					<td>{{ link_to_route('challenges.edit', 'Edit', array($challenge->id), array('class' => 'btn btn-info btn-margin')) }}
						{!! Form::open(array('method' => 'DELETE', 'route' => array('challenges.destroy', $challenge->id), 'style' => 'display: inline-block'))  !!}
							{!! Form::submit('Delete', array('class' => 'btn btn-danger btn-margin'))  !!}
						{!! Form::close()  !!}
					</td>
		</tr>
	</tbody>
</table>
<h4>Score Elements</h4>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
				<th>Display Text</th>
				<th>Order</th>
				<th>Base</th>
				<th>Multiplier</th>
				<th>Min</th>
				<th>Max</th>
                <th>Map</th>
				<th>Type</th>
				<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if( $challenge->score_elements->count() == 0 )
			<tr><td colspan="9" class="text-center">No Score Elements</td></tr>
		@else
		@foreach( $challenge->score_elements as $score_element)
			<tr>
				<td>{{{ $score_element->name }}}</td>
						<td>{{ $score_element->display_text }}</td>
						<td>{{{ $score_element->element_number }}}</td>
						<td>{{{ $score_element->base_value }}}</td>
						<td>{{{ $score_element->multiplier }}}</td>
						<td>{{{ $score_element->min_entry }}}</td>
						<td>{{{ $score_element->max_entry }}}</td>
                        <td>{{  count($score_element->score_map) ?: 'None' }}</td>
						<td>{{{ $score_element->type }}}</td>
						<td>{{ link_to_route('score_elements.edit', 'Edit', array($score_element->id), array('class' => 'btn btn-info btn_se_edit')) }}
							&nbsp;
							{!! Form::open(['method' => 'DELETE', 'route' => ['score_elements.destroy', $score_element->id], 'style' => 'display: inline-block'])  !!}
								{!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
							{!! Form::close()  !!}
						</td>
			</tr>
		@endforeach
		@endif
	</tbody>
</table>

<h4>Randoms</h4>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Type</th>
			<th>Format String</th>
			<th>Min/Max 1</th>
			<th>Min/Max 2</th>
			<th>Match</th>
			<th>Order</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if( $challenge->randoms->count() == 0 )
		<tr><td colspan="11" class="text-center">No Random Lists</td></tr>
		@else
			@foreach( $challenge->randoms as $random)
			<tr>
				<td>{{{ $random->name }}}</td>
				<td>{{{ $randoms::$types[$random->type] }}}</td>
				<td>{{{ $random->format }}}</td>
				<td>{{{ $random->min1 }}} to {{{ $random->max1 }}}</td>
				<td>{{{ $random->min2 }}} to {{{ $random->max2 }}}</td>
				<td>{{{ $random->may_not_match == 1 ? 'True':'False' }}}</td>
				<td>{{{ $random->display_order }}}</td>
				<td style="white-space:nowrap;">{{ link_to_route('randoms.edit', 'Edit', array($random->id), array('class' => 'btn btn-info btn_random_edit')) }}
					&nbsp;
					{!! Form::open(['method' => 'DELETE', 'route' => ['randoms.destroy', $random->id], 'style' => 'display: inline-block'])  !!}
						{!! Form::submit('Delete', array('class' => 'btn btn-danger'))  !!}
					{!! Form::close()  !!}
				</td>
			</tr>
			@endforeach
		@endif
	</tbody>
</table>

<h4>Random Lists</h4>
<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Name</th>
			<th>Format String</th>
			<th>Popup Format</th>
			<th>d1 Format</th>
			<th>d2 Format</th>
			<th>d3 Format</th>
			<th>d4 Format</th>
			<th>d5 Format</th>
			<th>Order</th>
			<th>Actions</th>
		</tr>
	</thead>

	<tbody>
		@if( count($challenge->random_lists) == 0 )
		<tr><td colspan="10" class="text-center">No Random Lists</td></tr>
		@else
			@foreach( $challenge->random_lists as $random_list)
			<tr>
				<td>{{ $random_list->name }}</td>
				<td>{{ $random_list->format }}</td>
				<td>{{ $random_list->popup_format }}</td>
				<td>{{ $random_list->d1_format }}</td>
				<td>{{ $random_list->d2_format }}</td>
				<td>{{ $random_list->d3_format }}</td>
				<td>{{ $random_list->d4_format }}</td>
				<td>{{ $random_list->d5_format }}</td>
				<td>{{{ $random_list->display_order }}}</td>
				<td style="white-space:nowrap;" class="text-center">
				{{ link_to_route('random_list.edit', 'Edit', array($random_list->id), array('class' => 'btn btn-info btn_random_list_edit btn-margin')) }}
					{!! Form::open(['method' => 'DELETE', 'route' => ['random_list.destroy', $random_list->id], 'style' => 'display: inline-block'])  !!}
						{!! Form::submit('Delete', array('class' => 'btn btn-danger btn-margin'))  !!}
					{!! Form::close()  !!}<br>
					{{ link_to_route('list_elements.edit', 'Edit Elements', array($random_list->id), array('class' => 'btn btn-success btn-margin btn_random_elements_edit')) }}
					{{ link_to_route('list_elements.show', 'Show Elements', array($random_list->id), array('class' => 'btn btn-warning btn-margin btn_random_elements_show')) }}
				</td>
			</tr>
			@endforeach
		@endif
	</tbody>
</table>

{!! Form::button('Add Score Element', array('class' => 'btn btn-primary', 'id' => 'add_score_element'))  !!}
&nbsp;
{!! Form::button('Add Random Element', array('class' => 'btn btn-info', 'id' => 'add_random_element'))  !!}
&nbsp;
{!! Form::button('Add Random List', array('class' => 'btn btn-success', 'id' => 'add_random_list'))  !!}

<div id="dialog" title="Score Elements">

</div>

<div id="random_dialog" title="Random Elements">

</div>

@endsection
