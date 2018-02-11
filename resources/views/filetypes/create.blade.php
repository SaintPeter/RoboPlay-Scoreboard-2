<tr id="type_add_row_{{ $unique_id }}">
    <td colspan="5">
        {!! Form::open(['route' => [ 'filetypes.store' ], 'class' => 'form-inline', 'id' => 'add_form_' . $unique_id ])  !!}
            <div class="form-group">
                <label for="ext">Ext</label>
                {!! Form::text('ext', null, [ 'class' => 'form-control',  'placeholder' => 'txt', 'style' => "width:75px" ])  !!}
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                {!! Form::select('type', $cat_list, $type, [ 'class' => 'form-control' ])  !!}
            </div>
            <div class="form-group">
                <label for="language">Lang</label>
                {!! Form::text('language', null, [ 'class' => 'form-control',  'placeholder' => 'c', 'style' => "width:50px" ])  !!}
            </div>
            <div class="form-group">
                <label for="viewer">Viewer</label>
                {!! Form::select('viewer', [ '' => 'None', 'lytebox' => 'Lytebox' ] , $type, [ 'class' => 'form-control' ])  !!}
            </div>
            <div class="form-group">
                <label for="icon">Icon</label>
                {!! Form::text('icon', null, [ 'class' => 'form-control',  'placeholder' => 'fa-file-o' ])  !!}
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button class="btn btn-info btn-sm  save_button btn-margin" data-id="{{ $unique_id }}">Save</button>
                <button class="btn btn-danger btn-sm cancel_button btn-margin" data-id="{{ $unique_id }}">
                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                </button>
            </div>
        {!! Form::close()  !!}
    </td>
</tr>