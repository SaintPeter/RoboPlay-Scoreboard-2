@if(isset($timer) AND $display_timer)
		var serverTime = moment("{{ $timer->start_time }}", "hh:mm:ss");
	var delta = moment().diff(serverTime);
	var endTime = moment("{{ $timer->next_event->start->toTimeString() }}", "hh:mm:ss");
	var sign = '';

	// Countdown Timer function
	var countdown_timer = setInterval(countdown_hander, 1000);

	function countdown_hander() {
		var now = moment().subtract('milliseconds', delta);
		$("#clock").html(now.format("h:mm:ss"));

		var timer = moment.duration(endTime.diff(now));
		if(timer.asSeconds() < 60 && timer.asSeconds() > 29 && !$('#timer').hasClass('label-warning')) {
			$('#timer').removeClass('label-info');
			$('#timer').addClass('label-warning');
		}
		if(timer.asSeconds() < 30 && !$('#timer').hasClass('label-danger')) {
			$('#timer').removeClass('label-warning');
			$('#timer').addClass('label-danger');
		}
	 	if(timer.asSeconds() < 0 && !$('#timer').hasClass('label-default')) {
	 		$('#timer').removeClass('label-danger');
	 		$('#timer').addClass('label-default');
	 		sign = '-';
	 	}

	 	if(timer.asSeconds() < -5) {
	 		location.reload(true);
	 	}
	 	$("#timer").html(sign + timer.hours() + ':' + prefix(Math.abs(timer.minutes())) + ':' + prefix(Math.abs(timer.seconds())));

 	}

	// 5 Minute Reload Timer
	setInterval( function() {
		location.reload(true);
	}, 5.2 * 60 * 1000); // 5.2 minutes * 60 seconds * 1000 Milliseconds

	// Add leading zero to single digits
	function prefix(input) {
	    return (input < 10 ? '0' : '') + input;
	}
@endif
