@extends('layouts.mobile')

@section('header', 'Choose Division')

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
<div id="menu-anchor"></div>
<ul data-role="listview">
	@foreach($divisions as $division)
		<li>{{ link_to_route('score.choose_team', $division->name, [$competition_id, $division->id], ['data-ajax' => "false"]) }}</li>
	@endforeach
</ul>
<script>
	$("#back_button").attr("href", "{{ route('score.choose_competition') }}");
</script>

@endsection