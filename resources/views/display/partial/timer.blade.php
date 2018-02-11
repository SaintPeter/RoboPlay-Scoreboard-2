@if($display_timer AND isset($timer))
	<div class="pull-right well well-sm timing col-md-6">
		<div class="clock_holder">
			<h1>
				<span id="clock" class="label label-primary">0:00:00</span>
				<small>{{ $timer->this_event->display }}</small>
			</h1>
		</div>
		<div class="timer_holder">
			<h1>
				<span id="timer" class="label label-info">0:00:00</span>
				<small>Next: {{ $timer->next_event->display }}</small>
			</h1>
		</div>
	</div>
@endif