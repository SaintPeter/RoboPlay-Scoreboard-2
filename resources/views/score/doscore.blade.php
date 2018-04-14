@extends('layouts.mobile')

@section('header', 'Score')

@section('style')
<style>
	.ui-li-static {
		white-space: normal; !important
	}
	.bigtext {
		font-size: 100px;
	}
	.center {
		text-align: center;
	}
	#abortPopup-popup, #submitPopup-popup, #randomPopup-popup, #randomListPopup-popup {
		width: 90%;
	}
</style>
@endsection

@section('navbar')
<a class="ui-btn-right"
   href="#"
   data-icon="back"
   data-iconpos="notext"
   role="button"
   id="back_button"
   data-ajax="false"
   >Back</a>
@endsection

@section('script')
<script>
	var val = "";

	$(function() {
		// Setup a function to recalculate the score on every "stop" event
		$("[id^=sel_]").change(calculate_score);
		// Calculate starting score
		calculate_score();

		// Submit button
		$('#do_submit').click(function() {
			$('#submit_action').attr('name','submit');
			$('#submit_button').click();
		});

		// Abort button
		$('#do_abort').click(function() {
			$('#submit_action').attr('name','abort');
			$('#submit_button').click();
		});
	});

	function calculate_score() {
		$('#score').html = "Blah";
		var total = 0;
		$('[id^=sel_]').each(function(i, obj) {
		    var element = $(obj)
			var base = parseInt(element.attr('base'));
			var val = parseInt(element.val());
			var multi = parseInt(element.attr('multi'));
            var subScore = base + ( val * multi);

			var map = [];
			var mapTemp = element.attr('map')
			if(mapTemp) {
			    map = mapTemp.split(/,/).map(function(subStr) {
			        var temp = subStr.split(/:/);
			        return { 'i': temp[0], 'v': temp[1] };
                })
            }
            for(var m = map.length - 1; m > -1; m--) {
			    if(subScore >= map[m].i) {
			        subScore = parseInt(map[m].v,10);
			        break;
                }
            }
			total += subScore;

		});
		total = Math.max(total, 0);
		$('.score').html(total);
	}
</script>
@endsection

@section('main')
<div class="ui-body ui-body-a" style="margin-bottom: 2em;">
	<p>
		<strong>Judge: </strong>{{ $user->name }}<br />
		<strong>Division: </strong>{{{ $team->division->name }}}<br />
		<strong>Team: </strong>{{{ $team->longname() }}}
	</p>
	<h2>Run {{{ $run_number }}}</h2>
	<p>
		<strong>{{ $challenge->divisions->find($team->division_id)->pivot->display_order }}.&nbsp;{{ $challenge->display_name }}</strong>
		<hr>
		{!! nl2br($challenge->rules)  !!}
        </p>
</div>

@if(count($challenge->randoms) > 0)
<div class="ui-body ui-body-a">
	<a href="#randomPopup" id="random_popout" data-rel="popup" data-position-to="window" class="ui-btn ui-btn-inline pull-right">Popout</a>
	<h4>Randoms</h4>
	<p>
	@foreach($challenge->randoms as $random)
		{!!  $random->formatted() !!}<br />
	@endforeach
	</p>
</div>
<div data-role="popup" data-history='false' id="randomPopup" class="ui-corner-all">
	<div role="banner" data-role="header" data-theme="a" class="ui-corner-top ui-header ui-bar-a">
		<h1 aria-level="1" role="heading" class="ui-title">Random Number</h1>
	</div>
	<div role="main" class="ui-corner-bottom ui-content center">
		@foreach($challenge->randoms as $random)
			<span class="bigtext">{!! $random->formatted()  !!} </span><br />
		@endforeach
	</div>
</div>
@endif
@if(count($challenge->random_lists) > 0)
<div class="ui-body ui-body-a">
	<a href="#randomListPopup" id="random_list_popout" data-rel="popup" data-position-to="window" class="ui-btn ui-btn-inline pull-right">Popout</a>
	<h4>Random Lists</h4>
	<p>
	@foreach($challenge->random_lists as $random)
		{!! $random->get_formatted() !!}<br />
	@endforeach
	</p>
</div>
<div data-role="popup" data-history='false' id="randomListPopup" class="ui-corner-all">
	<div role="banner" data-role="header" data-theme="a" class="ui-corner-top ui-header ui-bar-a">
		<h1 aria-level="1" role="heading" class="ui-title">Random List</h1>
	</div>
	<div role="main" class="ui-corner-bottom ui-content center">
		@foreach($challenge->random_lists as $random)
			<span class="bigtext">{!! $random->get_formatted_popup()  !!}</span><br />
		@endforeach
	</div>
</div>
@endif
<br />
{!! Form::open(array('route' => array('score.save', $team->id, $challenge->id), 'method' => 'post', 'id' => 'se_form', 'data-ajax' => 'false' ))  !!}
	<ul data-role="listview">
		@foreach($score_elements as $id => $score_element)
			@if ($score_element->type == 'yesno')
				@include('score.partial.yesno', compact('score_element'))
			@elseif ($score_element->type == 'noyes')
				@include('score.partial.noyes', compact('score_element'))
			@elseif ($score_element->type == 'slider' OR $score_element->type == 'low_slider')
				@include('score.partial.low_slider', compact('score_element'))
			@elseif ($score_element->type == 'high_slider')
				@include('score.partial.high_slider', compact('score_element'))
			@elseif ($score_element->type == 'score_slider')
				@include('score.partial.score_slider', compact('score_element'))
			@else
				<li>Error displaying Score Element '{{ $score_element->display_text }} ({{ $score_element->element_number }})'</li>
			@endif
		@endforeach
		<li>
			Estimated Score: <span class="score"></span> out of {{ $challenge->points }} points
		</li>
		<li>
			<fieldset class="ui-grid-b">
				<div class="ui-block-b"><a href="#submitPopup" class="ui-btn ui-corner-all ui-shadow" id="submitPopup_button" data-role="button" data-rel="popup" data-position-to="window">Submit</a></div>
				<div class="ui-block-b">{!! Form::submit('Cancel', array('class' => 'ui-btn', 'name' => 'cancel'))  !!}</div>
				<div class="ui-block-b"><a href="#abortPopup" class="ui-btn ui-corner-all ui-shadow" id="abortPopup_button" data-role="button" data-rel="popup" data-position-to="window">Abort</a></div>

			</fieldset>
		</li>
		<input type="submit" data-role="none" name="blah" id="submit_button" style="display: none;">
		<input type="hidden" name="changeme" id="submit_action" value="1">
	</ul>

<div data-role="popup" data-history='false' id="submitPopup" class="ui-corner-all">
	<div role="banner" data-role="header" data-theme="a" class="ui-corner-top ui-header ui-bar-a">
		<h1 aria-level="1" role="heading" class="ui-title">Confirm Submit?</h1>
	</div>
	<div role="main" class="ui-corner-bottom ui-content center">
		<span class="bigtext">Run {{{ $run_number }}}</span><br />
		<span class="bigtext">Score: <span class="score" style="color:blue;"></span></span><br />
		<a role="button" class="ui-link ui-btn ui-btn-a ui-btn-inline ui-shadow ui-corner-all" href="#" data-role="button" data-inline="true" data-rel="back" data-theme="a">Cancel</a>
		<a role="button" id="do_submit" class="ui-link ui-btn ui-btn-b ui-btn-inline ui-shadow ui-corner-all" href="#" data-role="button" data-inline="true" data-transition="flow" data-theme="b" data-ajax="false">Confirm Submit</a>
	</div>
</div>

<div data-role="popup" data-history='false' id="abortPopup" class="ui-corner-all">
	<div role="banner" data-role="header" data-theme="a" class="ui-corner-top ui-header ui-bar-a">
		<h1 aria-level="1" role="heading" class="ui-title">Confirm Abort?</h1>
	</div>
	<div role="main" class="ui-corner-bottom ui-content center">
		<span class="bigtext">Run {{{ $run_number }}}</span><br />
		<span class="bigtext" style="color: red;">Abort</span><br />
		<a role="button" class="ui-link ui-btn ui-btn-a ui-btn-inline ui-shadow ui-corner-all" href="#" data-role="button" data-inline="true" data-rel="back" data-theme="a">Cancel</a>
		<a role="button" id="do_abort" class="ui-link ui-btn ui-btn-b ui-btn-inline ui-shadow ui-corner-all" href="#" data-role="button" data-inline="true" data-rel="back" data-transition="flow" data-theme="b">Confirm Abort</a>
	</div>
</div>


{!! Form::close()  !!}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif

<script>
	$("#back_button").attr("href", "{{ route('score.score_team', [$competition_id, $division_id, $team->id]) }}");
</script>

@endsection