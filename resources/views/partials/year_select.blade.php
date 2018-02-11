
<div class="pull-right">
	<ul class="nav nav-pills">
	    <?php $year = isset($year) ? intval($year) : Session::get('year', false); ?>
		@for($year_counter = 2014; $year_counter <= Carbon\Carbon::now()->year; $year_counter++)
			<li @if($year_counter == $year) class="active" @endif>{{ link_to_route(Route::currentRouteName(),  $year_counter, array_merge(Route::current()->parameters(), [ 'year' => $year_counter ] ) ) }}</li>
		@endfor
		@if($year)
			<li>
				<a href="{{ route(Route::currentRouteName(), [ 'year' => '' ]) }}">
					<span class="glyphicon glyphicon-remove"></span>
				</a>
			</li>
		@endif
	</ul>
</div>