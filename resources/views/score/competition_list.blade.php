@extends('layouts.mobile')

@section('header', 'Choose Competition')

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
<ul data-role="listview">
	@foreach($competitions as $competition)
		<li>{{ link_to_route('score.choose_division', $competition->name , [ $competition->id ], ['data-ajax' => "false"]) }}</li>
	@endforeach
</ul>

<script>
	$("#back_button").attr("href", "{{ route('home') }}");
</script>

@endsection