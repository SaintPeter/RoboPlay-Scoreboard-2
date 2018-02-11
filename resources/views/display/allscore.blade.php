@extends('layouts.scaffold', [ 'fluid' => true ])

@section('head')
	<META HTTP-EQUIV="refresh" CONTENT="120">
	{{ HTML::script('js/moment.min.js') }}
	{{ HTML::style('//cdn.jsdelivr.net/jquery.slick/1.5.0/slick.css') }}
	{{ HTML::script('//cdn.jsdelivr.net/jquery.slick/1.5.0/slick.min.js') }}
@endsection

@section('script')
<script>

    @include('display.partial.timerjs', [ 'timer' => $timer, 'display_timer' => $display_timer ])
	$(function(){
		$('#slick_container').slick({
			slidesToShow: {{ $settings['columns'] }},
			autoplay: true,
			autoplaySpeed:  {{ $settings['delay'] }},
			speed: 5000,
			pauseOnHover: false,
			prevArrow: '',
			nextArrow: ''
		});

		$("#show_settings").click(function(e) {
			e.preventDefault();
			$("#dialog-settings").dialog('open');
		});

		$('#toggle_pause').on('click', function(e) {
			e.preventDefault();
			var slick = $('#slick_container').slick('getSlick');
			$(this).toggleClass('active');
			if(slick.paused) {
			    slick.slickPlay();
			    $(this).children('i').removeClass('fa-play').addClass('fa-pause');
			} else {
			    $(this).children('i').removeClass('fa-pause').addClass('fa-play');
			    slick.slickPause();
			}
		});

		$( "#dialog-settings" ).dialog({
			resizable: false,
			autoOpen: false,
			width:320,
			buttons: {
				"Apply Settings": function() {
					$( this ).dialog( "close" );
					$('#settings_form').submit();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
</script>
@endsection

@section('style')
	.bold_row > td {
		font-weight: bold;
	}
	.clock_holder, .timer_holder {
		text-align: left;
	}
	.timing {
		margin-left: 35px;
		margin-right: 0px
		whitespace: nobreak;
	}
	.timing h1:first-child {
		margin-top: 0px;
	}
	.header_container {
		margin: 5px 15px;
	}
	#slick_container {
		font-size: {{ $settings['font-size'] }};
	}
@endsection

<?php View::share( [ 'skip_title' => true, 'skip_breadcrumbs' => true ] ); ?>
@section('before_header')
	<div class="clearfix header_container">
		@include('display.partial.timer', [ 'timer' => $timer, 'display_timer' => $display_timer ] )
		<h1>{{ $title }}</h1>
		{{ link_to_route('home', 'Home', null, [ 'class' => 'btn btn-primary btn-margin' ]) }}
		<a href="#" id="show_settings" class="btn btn-info btn-margin"><span class="glyphicon glyphicon-cog"></span></a>
    	<a href="#" id="toggle_pause" class="btn btn-warning btn-margin"><i class="fa fa-pause"></i></a>
    	@if($frozen)
    	    @if(Roles::isAdmin())
                {{ link_to_route('competition.toggle_frozen', "Scores Frozen since " . $freeze_time->format('g:i a'), [ $comp->id ], ['class' => 'btn btn-lg btn-info', 'title' => 'Click to Unfreeze' ] ) }}
            @else
                <span class="btn btn-lg btn-info">Scores Frozen since {{ $freeze_time->format('g:i a') }}</span>
            @endif
    	@endif
	</div>
@endsection

@section('main')
<div id="slick_container">
	<div class="col-md-12 col-lg-12">
		<table class="table table-striped table-bordered table-condensed">
			<tr class="info">
				<td>School</td>
				<td>Team</td>
				<td>Score (Runs)</td>
			</tr>
			<?php $rowcount = 1; ?>
			@foreach($score_list as $team_id => $score)
				<?php $rowcount++; ?>
				<tr>
					<td>
						{{ $score['school'] }}
					</td>
					<td>
						{{ link_to_route('display.teamscore', $score['name'], $team_id) }}
					</td>
					<td>
						{{ $score['total'] }} ({{ $score['runs'] }})
					</td>
				</tr>
				@if($rowcount > $settings['rows'])
					</table>
				</div>
				<div class="col-md-12 col-lg-12">
				<table class="table table-striped table-bordered table-condensed">
					<tr class="info">
    					<td>School</td>
    					<td>Team</td>
    					<td>Score (Runs)</td>
    				</tr>
    				<?php $rowcount = 1; ?>
				@endif
			@endforeach
		</table>
	</div>
</div>

@include('display.partial.settings', [ 'route' => 'display.all_scores_settings', 'id' => $compyear->id ])

@endsection