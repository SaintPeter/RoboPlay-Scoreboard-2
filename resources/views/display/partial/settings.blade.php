<div id="dialog-settings" title="Adjust Settings">
	{!! Form::open( [ 'route' => [ $route, $id ], 'class' => 'form-horizontal', 'id' => 'settings_form', 'style' => 'margin: 5px;' ] )  !!}
		<div class="form-group">
			{!! Form::label('columns', 'Columns:', [ 'class' => 'col-sm-4 control-label' ])  !!}
			<div class="col-sm-7">
				{!! Form::text('columns', $settings['columns'] , [ 'class'=>'form-control' ])  !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('rows', 'Rows:', [ 'class' => 'col-sm-4 control-label' ])  !!}
			<div class="col-sm-7">
				{!! Form::text('rows', $settings['rows'] , [ 'class'=>'form-control' ])  !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('delay', 'Delay (ms):', [ 'class' => 'col-sm-4 control-label' ])  !!}
			<div class="col-sm-7">
				{!! Form::text('delay', $settings['delay'] , [ 'class'=>'form-control' ])  !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('font-size', 'Font Size:', [ 'class' => 'col-sm-4 control-label' ])  !!}
			<div class="col-sm-7">
				{!! Form::text('font-size', $settings['font-size'] , [ 'class'=>'form-control' ])  !!}
			</div>
		</div>

	{!! Form::close()  !!}
</div>