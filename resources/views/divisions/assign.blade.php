@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('/css/multi-select.css') }}
	{{ HTML::script('/js/jquery.multi-select.js') }}
@endsection

@section('script')
<script>
	$(function() {
		jQuery("#has_list").multiSelect({
			selectableHeader: "<div class='panel-title'>All Challenges</div>",
  			selectionHeader: "<div class='panel-title'>In Division</div>"
  		});

  		// Level Selector
		$('#level_select').change(function (e) {
			window.location = "{{ route('divisions.assign', [ $division_id ] ) }}" +  "?level_select=" + $(this).val();
		});
	});
</script>
@endsection

@section('main')
<div class="content col-md-8">
	<div class="clearfix">
		@include('partials.year_select')

		<div class="pull-right" style="margin-right: 10px">
			{!! Form::select('level_select', Challenge::$levels, Session::get('level_select', 0), [ 'class' => 'form-control', 'id' => 'level_select' ])  !!}
		</div>
	</div>

	{!! Form::open(array('route' =>'divisions.saveassign'))  !!}
		{!! Form::select('has[]', $all_list, $selected_list, array('id' => 'has_list','multiple'=>'multiple'))  !!}
		{!! Form::hidden('division_id', $division_id)  !!}
		{!! Form::submit('Submit', array('class' => 'btn btn-primary btn-margin'))  !!}
		{{ link_to_route('divisions.show', 'Cancel', [ $division_id ], [ 'class' => 'btn btn-info btn-margin' ]) }}
	{!! Form::close()  !!}
</div>
@endsection