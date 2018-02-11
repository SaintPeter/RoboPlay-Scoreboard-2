{!! Form::model($random_list, [  'route' => [ 'random_list.update', $random_list->id ], 'method' => 'patch', 'id' => 'random_list_form' ])  !!}
	<div class="form-group">
		{!! Form::label('name', 'Name', [ 'class' => 'form-label' ])  !!}
		{!! Form::text('name', null, [ 'class' => 'form-control' ])  !!}
	</div>
	<p>Values {d1} through {d5} will be replaced with formatted output.</p>
	<div class="form-group">
		{!! Form::label('format', 'Format', [ 'class' => 'form-label' ])  !!}
		{!! Form::textarea('format', null, [ 'class' => 'form-control', 'rows' => 3 ])  !!}
	</div>
	<div class="form-group">
		{!! Form::label('popup_format', 'Popup Format', [ 'class' => 'form-label' ])  !!}
		{!! Form::textarea('popup_format', null, [ 'class' => 'form-control', 'rows' => 3 ])  !!}
	</div>
<p>Use <a href="http://php.net/manual/en/function.sprintf.php" target="_blank">sprintf()</a> specifiers</p>
	<div class="row">
    	<div class="form-group col-md-12">
    	    <div class="row">
        		<div class="col-md-6">
        		{!! Form::label('d1_format', 'd1 Format', [ 'class' => 'form-label' ])  !!}
        		{!! Form::text('d1_format', null, [ 'class' => 'form-control' ])  !!}
        		</div>

            	<div class="col-md-6">
        		{!! Form::label('d2_format', 'd2 Format', [ 'class' => 'form-label' ])  !!}
        		{!! Form::text('d2_format', null, [ 'class' => 'form-control' ])  !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    	<div class="form-group col-md-12">
    	    <div class="row">
        		<div class="col-md-6">
            		{!! Form::label('d3_format', 'd3 Format', [ 'class' => 'form-label' ])  !!}
            		{!! Form::text('d3_format', null, [ 'class' => 'form-control' ])  !!}
                </div>
    	        <div class="col-md-6">
            		{!! Form::label('d4_format', 'd4 Format', [ 'class' => 'form-label' ])  !!}
            		{!! Form::text('d4_format', null, [ 'class' => 'form-control' ])  !!}
                </div>
            </div>
        </div>
    </div>
    <div class="row">
    	<div class="form-group col-md-12">
    	    <div class="row">
        		<div class="col-md-6">
            		{!! Form::label('d5_format', 'd5 Format', [ 'class' => 'form-label' ])  !!}
            		{!! Form::text('d5_format', null, [ 'class' => 'form-control' ])  !!}
                </div>
                <div class="col-md-6"></div>
            </div>
        </div>
    </div>

	<div class="form-group">
		{!! Form::label('display_order', 'Display Order', [ 'class' => 'form-label' ])  !!}
		{!! Form::text('display_order', null, [ 'class' => 'form-control numeric' ])  !!}
	</div>


	<div class="form-group">
		{!! Form::input('hidden', 'challenge_id', $random_list->challenge_id)  !!}
		{!! Form::submit('Submit', array('class' => 'btn btn-primary random_list_submit'))  !!}
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