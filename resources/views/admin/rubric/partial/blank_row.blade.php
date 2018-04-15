<div class="rubric_edit_row row delta">
    {{ Form::hidden("rubric[$rowId][vid_score_type_id]", $vid_score_type_id, [ 'class' => 'type_id' ]) }}
    {{ Form::hidden("rubric[$rowId][vid_competition_id]", $competition_id, [ 'class' => 'competition_id' ]) }}
    {{ Form::hidden("rubric[$rowId][order]",99, [ 'class' => 'order' ]) }}
    {{ Form::hidden("rubric[$rowId][delta]",1, [ 'class' => 'delta' ]) }}
    {{ Form::hidden("rubric[$rowId][new]",1, [ 'class' => 'new' ]) }}
    {{ Form::hidden("rubric[$rowId][delete]",0, [ 'class' => 'delete' ]) }}
    <div class="rubric_movement_controls">
        <button class="btn btn-primary btn-sm rubric_button" data-direction="up">
            <i class="fa fa-chevron-up"></i>
        </button>
        <button class="btn btn-primary btn-sm rubric_button" data-direction="down">
            <i class="fa fa-chevron-down"></i>
        </button>
    </div>
    <div class="rubric_element_and_delete">
        <input class="form-control" type="text" name="rubric[{{ $rowId }}][element_name]" placeholder="Element Name"></input>
        <button class="btn btn-danger btn-sm rubric_delete_blank" title="Delete">
            <i class="fa fa-times"></i>
            Delete
        </button>
    </div>
    <textarea rows="5" class="form-control" name="rubric[{{ $rowId }}][zero]"></textarea>
    <textarea rows="5" class="form-control" name="rubric[{{ $rowId }}][one]"></textarea>
    <textarea rows="5" class="form-control" name="rubric[{{ $rowId }}][two]"></textarea>
    <textarea rows="5" class="form-control" name="rubric[{{ $rowId }}][three]"></textarea>
    <textarea rows="5" class="form-control" name="rubric[{{ $rowId }}][four]"></textarea>
</div>