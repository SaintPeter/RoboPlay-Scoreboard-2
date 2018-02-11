<tr id="type_edit_row_{{ $type->id }}">
    <td colspan="5">
        {!! Form::model($type, ['route' => [ 'filetypes.update', $type->id ], 'class' => 'form-inline', 'id' => 'edit_form_' . $type->id ])  !!}
            <input name="_method" type="hidden" value="PUT">
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
                <button class="btn btn-info btn-sm  update_button btn-margin" data-id="{{ $type->id }}">Update</button>
                <button class="btn btn-warning btn-sm cancel_update_button btn-margin" data-target="{{ route('filetypes.show', [ $type->id ]) }}" ]" data-id="{{ $type->id }}">
                    Cancel
                </button>
            </div>
        {!! Form::close()  !!}
    </td>
</tr>