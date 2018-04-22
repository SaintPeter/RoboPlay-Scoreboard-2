@if ($errors->any())
<div class="col-md-12">
	<h3>Validation Errors</h3>
	<ul>
		{!!  implode('', $errors->all('<li class="error">:message</li>')) !!}
	</ul>
</div>
@endif
{!! Form::open(array('route' => 'score_elements.store', 'id' => 'se_form'))  !!}
<div id="maincol" class="{{ ($has_score_map) ? 'col-lg-8':'col-lg-12'}}">
	<div class="form-group row">
        <div class="col-xs-3" style="padding-left: 0;padding-right: 5px">
            {!! Form::label('element_number', 'Order', [ 'class' => 'form-label' ])  !!}
            {!! Form::text( 'element_number', $order, [ 'class' => 'form-control numeric' ])  !!}
        </div>
        <div class="col-xs-9" style="padding-right: 0; padding-left: 5px;">
            {!! Form::label('name', 'Name', [ 'class' => 'form-label' ])  !!}
            {!! Form::text('name', null, [ 'class' => 'form-control' ])  !!}
        </div>
	</div>

	<div class="form-group row">
		{!! Form::label('display_text', 'Display Text', [ 'class' => 'form-label' ])  !!}
		{!! Form::textarea('display_text', null, [ 'class' => 'form-control', 'rows' => 3 ])  !!}
	</div>

	<div class="form-group row">
		<div class="col-xs-6" style="padding-left: 0;">
			{!! Form::label('type', 'Input Type', [ 'class' => 'form-label' ])  !!}
			{!! Form::select('type', $input_types, 0, [ 'class' => 'form-control' ])  !!}
		</div>
		<div class="col-xs-6" style="padding-right: 0;">
		{!! Form::label('base_value', 'Base Value', [ 'class' => 'form-label' ])  !!}
		{!! Form::input('number', 'base_value', 0, [ 'class' => 'form-control numeric' ])  !!}
		</div>
	</div>


	<div class="form-group row">
		 <div class="col-xs-6" style="padding-left: 0;">
		{!! Form::label('multiplier', 'Multiplier 1', [ 'class' => 'form-label' ])  !!}
		{!! Form::input('number', 'multiplier', 1, [ 'class' => 'form-control decimal' ])  !!}
		</div>
        <div class="col-xs-6" style="padding-right: 0;">
            {!! Form::label('multiplier', 'Multiplier 2', [ 'class' => 'form-label' ])  !!}
            {!! Form::input('number', 'multiplier2', 0, [ 'class' => 'form-control decimal' ])  !!}
        </div>
	</div>

	<div class="form-group row">
		<div class="col-xs-6" style="padding-left: 0;">
			{!! Form::label('min_entry', 'Minimum Value', [ 'class' => 'form-label' ])  !!}
			{!! Form::text( 'min_entry', 0, [ 'class' => 'form-control numeric' ])  !!}
		</div>
		<div class="col-xs-6" style="padding-right: 0;">
			{!! Form::label('max_entry', 'Maximum Value', [ 'class' => 'form-label' ])  !!}
			{!! Form::text( 'max_entry', 100, [ 'class' => 'form-control numeric' ])  !!}
		</div>
	</div>

    <div class="form-group row">
        <div class="checkbox">
            {!! Form::hidden('enforce_limits',0) !!}
            <label>
                {!! Form::checkbox('enforce_limits',1) !!}
                Enforce Min/Max Limits
            </label>
        </div>
    </div>


	<div class="form-group row">
		{!! Form::hidden('challenge_id', $challenge_id)  !!}
		{!! Form::submit('Submit', array('class' => 'btn btn-info se_submit'))  !!}
        {!! Form::button('Create Map', array('class' => 'btn btn-primary random_submit', 'id' => 'edit_map'))  !!}
	</div>
</div>
{!! Form::hidden('has_score_map', $has_score_map) !!}
<div id="mapcol" class="col-lg-4 text-center{{ ($has_score_map) ? '' : ' hidden' }}">
    <table class="score_map">
        <thead>
            <tr>
                <th>Value</th>
                <th>Maps To</th>
            </tr>
        </thead>
        <tbody>
        @foreach($score_map as $map)
            <tr id="score_map_row_{{ $loop->index }}">
                <td>{!! Form::text('score_map[' . $loop->index . '][i]', $score_map[$loop->index]['i'], [ 'class' => 'numeric text-center']) !!}</td>
                <td>{!! Form::text('score_map[' . $loop->index . '][v]', $score_map[$loop->index]['v'], [ 'class' => 'numeric text-center']) !!}</td>
                <td>
                    @if($loop->index > 1)
                        <a href="javascript:void(0)" class="btn btn-xs delete_row" data-index="{{$loop->index}}" title="Delete Row">
                            <span class="glyphicon glyphicon-minus text-danger" aria-hidden="true"></span>
                        </a>
                    @else
                        &nbsp;
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <a id="add_row" href="javascript:void(0)" class="btn btn-xs btn-default add_row" data-index="{{ count($score_map) }}" title="Add Row">
        Add Row <span class="glyphicon glyphicon-plus text-primary" aria-hidden="true"></span>
    </a>
</div>
{!! Form::close()  !!}


