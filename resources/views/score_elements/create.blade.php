@if ($errors->any())
<div class="col-md-12">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif
{!! Form::open(array('route' => 'score_elements.store', 'id' => 'se_form'))  !!}
	<div class="form-group">
		{!! Form::label('name', 'Name', [ 'class' => 'form-label' ])  !!}
		{!! Form::text('name', null, [ 'class' => 'form-control' ])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('display_text', 'Display Text', [ 'class' => 'form-label' ])  !!}
		{!! Form::textarea('display_text', null, [ 'class' => 'form-control', 'rows' => 3 ])  !!}
	</div>

	<div class="form-group clearfix">
		<div class="col-md-6" style="padding-left:0px">
			{!! Form::label('element_number', 'Display Order', [ 'class' => 'form-label' ])  !!}
			{!! Form::text( 'element_number', $order, [ 'class' => 'form-control numeric' ])  !!}
		</div>
		<div class="col-md-6" style="padding-left:0px">
			{!! Form::label('type', 'Input Type', [ 'class' => 'form-label' ])  !!}
			{!! Form::select('type', $input_types, null, [ 'class' => 'form-control' ])  !!}
		</div>
	</div>

	<div class="form-group clearfix">
		 <div class="col-md-6" style="padding-left:0px">
		{!! Form::label('multiplier', 'Multiplier', [ 'class' => 'form-label' ])  !!}
		{!! Form::input('number', 'multiplier', null, [ 'class' => 'form-control numeric' ])  !!}
		</div>
		<div class="col-md-6" style="padding-left:0px">
		{!! Form::label('base_value', 'Base Value', [ 'class' => 'form-label' ])  !!}
		{!! Form::input('number', 'base_value', null, [ 'class' => 'form-control numeric' ])  !!}
		</div>
	</div>

	<div class="form-group clearfix">
		<div class="col-md-6" style="padding-left:0px">
			{!! Form::label('min_entry', 'Minimum Value', [ 'class' => 'form-label' ])  !!}
			{!! Form::text( 'min_entry', null, [ 'class' => 'form-control numeric' ])  !!}
		</div>
		<div class="col-md-6" style="padding-left:0px">
			{!! Form::label('max_entry', 'Maximum Value', [ 'class' => 'form-label' ])  !!}
			{!! Form::text( 'max_entry', null, [ 'class' => 'form-control numeric' ])  !!}
		</div>
	</div>

	<div class="form-group">
		{!! Form::input('hidden', 'challenge_id', $challenge_id)  !!}
		{!! Form::submit('Submit', array('class' => 'btn btn-info se_submit'))  !!}
	</div>
{!! Form::close()  !!}


