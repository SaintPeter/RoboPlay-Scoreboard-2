{!! Form::model($random, [ 'route' => [ 'randoms.update', $random->id ], 'method' => 'patch', 'id' => 'random_form' ])  !!}
	<div class="form-group">
		{!! Form::label('name', 'Name', [ 'class' => 'form-label' ])  !!}
		{!! Form::text('name', null, [ 'class' => 'form-control' ])  !!}
	</div>
	<div class="form-group">
		{!! Form::label('type', 'Type', [ 'class' => 'form-label' ])  !!}
		{!! Form::select('type', Random::$types, $random->type, [ 'class' => 'form-control' ])  !!}
	</div>
	<div class="form-group">
		{!! Form::label('format', 'Format', [ 'class' => 'form-label' ])  !!}
		{!! Form::text('format', null, [ 'class' => 'form-control' ])  !!}
	</div>
	<div class="row">
    	<div class="form-group col-md-12">
    	    <div class="row">
        		<div class="col-md-6">
        			{!! Form::label('min1', 'Min 1', [ 'class' => 'form-label' ])  !!}
        			{!! Form::text('min1', null, [ 'class' => 'form-control numeric' ])  !!}
        		</div>
        		<div class="col-md-6">
        			{!! Form::label('max1', 'Max 1', [ 'class' => 'form-label' ])  !!}
        			{!! Form::text('max1', null, [ 'class' => 'form-control numeric' ])  !!}
        		</div>
    		</div>
    	</div>
	</div>
	<div class="row">
    	<div class="form-group col-md-12">
    	    <div class="row">
        		<div class="col-md-6">
        			{!! Form::label('min2', 'Min 2', [ 'class' => 'form-label' ])  !!}
        			{!! Form::text('min2', null, [ 'class' => 'form-control numeric' ])  !!}
        		</div>
        		<div class="col-md-6">
        			{!! Form::label('max2', 'Max 2', [ 'class' => 'form-label' ])  !!}
        			{!! Form::text('max2', null, [ 'class' => 'form-control numeric' ])  !!}
        		</div>
        	</div>
	    </div>
	</div>
	<div class="checkbox">
		{!! Form::hidden('may_not_match', 0)  !!}
		<label class="form-label">May not Match</label>
		{!! Form::checkbox('may_not_match', true)  !!}
	</div>

	<div class="form-group">
		{!! Form::label('display_order', 'Display Order', [ 'class' => 'form-label' ])  !!}
		{!! Form::text('display_order', null, [ 'class' => 'form-control numeric' ])  !!}
	</div>


	<div class="form-group">
		{!! Form::input('hidden', 'challenge_id', $random->challenge_id)  !!}
		{!! Form::submit('Save', array('class' => 'btn btn-primary random_submit'))  !!}
	</div>

{!! Form::close()  !!}

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{{ implode('', $errors->all('<li class="error">:message</li>')) }}
	</ul>
</div>
@endif