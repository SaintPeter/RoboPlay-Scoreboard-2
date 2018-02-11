@extends('layouts.mobile')

@section('header','Choose Team')

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

@section('main')
<ul data-role="listview" data-filter="true" data-filter-placeholder="Search teams..." data-inset="true">
	@foreach($teams as $team)
		<li>{{ link_to_route('score.score_team', $team->longname(), [$competition_id, $division_id, $team->id ], array('data-ajax' => 'false')) }}</li>
	@endforeach
</ul>

<script>
	$("#back_button").attr("href", "{{ route('score.choose_division', [$competition_id]) }}");
</script>

@endsection