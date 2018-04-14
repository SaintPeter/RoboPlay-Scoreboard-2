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
                slick.slickSetOption('speed',5000);
			} else {
			    $(this).children('i').removeClass('fa-pause').addClass('fa-play');
			    slick.slickPause();
                slick.slickSetOption('speed',500);
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
<style>
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
</style>
@endsection

<?php View::share( [ 'skip_title' => true, 'skip_breadcrumbs' => true ] ); ?>
@section('before_header')
	<div class="clearfix header_container">
		@include('display.partial.timer', [ 'timer' => $timer, 'display_timer' => $display_timer ] )
		<h1>{{ $title }}</h1>
		{{ link_to_route('home', 'Home', null, [ 'class' => 'btn btn-primary btn-margin' ]) }}
		@if($top)
		    {{ link_to_route('display.compscore', 'All Scores', [ $comp->id ], [ 'class' => 'btn btn-danger btn-margin' ]) }}
		@else
		    {{ link_to_route('display.compscore.top', 'Leaders', [ $comp->id ], [ 'class' => 'btn btn-danger btn-margin' ]) }}
		@endif
		<a href="#" id="show_settings" class="btn btn-info btn-margin"><span class="glyphicon glyphicon-cog"></span></a>
		<a href="{{ route('display.compscore' . (($top) ? '.top' : ''), [ $comp->id, 'csv' ]) }}" id="download_csv" class="btn btn-success btn-margin" title="Download scores as CSV">
			<i class="fa fa-file-excel-o"></i>
		</a>
    	<a href="#" id="toggle_pause" class="btn btn-warning btn-margin"><i class="fa fa-pause"></i></a>
	</div>
@endsection

@section('main')
<div id="slick_container">
	<div class="col-md-12 col-lg-12">
		<table class="table table-striped table-bordered table-condensed">
			<?php $rowcount = 0; ?>
			@foreach($divisions as $division)
				<tr class="info">
					<td colspan="4">{{ $division->name }} Division</td>
				</tr>
				<tr class="bold_row">
					<td>#</td>
					<td>Team</td>
					<td>School</td>
					<td>Score (Runs)</td>
				</tr>
				<?php $rowcount += 2; ?>
				@foreach($score_list[$division->id] as $team_id => $score)
					<?php $rowcount++; ?>
					<tr>
						<td>{{ $score['place'] }}</td>
						<td>
							{{ link_to_route('display.teamscore', $division->teams->find($team_id) ? $division->teams->find($team_id)->name : 'Not Found', $team_id) }}
						</td>
						<td>
							{{ $division->teams->find($team_id) ? $division->teams->find($team_id)->school->name : 'Not Found' }}
						</td>
						<td>
							{{ $score['total'] }} ({{ $score['runs'] }})
						</td>
					</tr>
					@if($rowcount >  $settings['rows'])
						</table>
					</div>
					<div class="col-md-12 col-lg-12">
					<table class="table table-striped table-bordered table-condensed">

						<tr class="info">
							<td colspan="4">{{ $division->name }} Division</td>
						</tr>
						<tr class="bold_row">
							<td>#</td>
							<td>Team</td>
							<td>School</td>
							<td>Score (Runs)</td>
						</tr>
						<?php $rowcount = 2; ?>
					@endif
				@endforeach
			@endforeach
		</table>
	</div>
</div>

@include('display.partial.settings', [ 'route' => 'display.compsettings', 'id' => $comp->id ]);

@endsection