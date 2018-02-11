{!! Form::open([ 'route' => [ 'list_elements.save', $random_list_id ], 'method' => 'post', 'id' => 'random_list_form' ])  !!}
    <div class="form-group">
        {!! Form::label('elements', 'Elements')  !!}
        {!! Form::textarea('elements', $elements, [ 'class' => 'form-control', 'multiline' => 'multiline', 'rows' => '10', 'cols' => '40' ])  !!}
    </div>
    <div class="form-group">
		{!! Form::submit('Save', array('class' => 'btn btn-primary random_submit'))  !!}
	</div>
{!! Form::close()  !!}