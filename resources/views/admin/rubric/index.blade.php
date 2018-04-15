@extends('layouts.scaffold')

@section('script')
<script>
    var delta = false;
    var nextId = 1000000;
    $(document).ready(function(){
        $('#view').on('click', viewClickHandler);
        $('#edit').on('click', editClickHandler);
        $('#copyTo').on('click', copyToClickHandler);

        $(document).on('change', 'textarea,input', deltaHandler);
        $(document).on('click', '.rubric_delete', toggleDelete);
        $(document).on('click', '.rubric_delete_blank', deleteBlank);
        $(document).on('click', '.rubric_button', movementClickHandler);
        $(document).on('click', '.rubric_add_row', addRowClickHandler)

    });

    function movementClickHandler(e) {
        e.preventDefault();
        var button = $(this);
        var parentRow = button.parents('.rubric_edit_row');
        var category = parentRow.parent('.rubric_category');
        if(button.data('direction') == 'up') {
            if(parentRow.prev('.rubric_edit_row')) {
                parentRow.insertBefore(parentRow.prev('.rubric_edit_row'));
                reorder(category);
            }
        } else {
            if(parentRow.next('.rubric_edit_row')) {
                parentRow.insertAfter(parentRow.next('.rubric_edit_row'));
                reorder(category);
            }
        }
    }

    function copyToClickHandler(e) {
        e.preventDefault();
        var comp_id = $('select#comp_select option:checked').val();
        var dest_id = $('select#dest_select option:checked').val();
        window.location = '/rubric/' + comp_id + '/copyto/' + dest_id;
    }

    function reorder(category) {
        var rows = category.children('.rubric_edit_row');
        var newOrder = 1;
        rows.each(function(index, row) {
            var order = $(row).find('.order');
            if(order.val() != newOrder) {
                order.val(newOrder);
                setDelta($(row));
            }
            newOrder++;
        });

    }

    function deltaHandler(e) {
        setDelta($(this).parent('.rubric_edit_row'));
    }

    function setDelta(row) {
        row.find('.delta').val(1);
        if(!row.hasClass('delta')) {
            row.addClass('delta');
        }
    }

    function toggleDelete(e) {
        e.preventDefault();
        var button = $(this);
        var row = button.parents('.rubric_edit_row');

        if(row.find('.delete').val() == 1) {
            row.find('.delete').val(0);
            row.removeClass('delete');
            button.removeClass('btn-success').addClass('btn-danger');
            button.html(button.html().replace('Restore', 'Delete'));
        } else {
            row.find('.delete').val(1);
            row.addClass('delete');
            button.removeClass('btn-danger').addClass('btn-success');
            button.html(button.html().replace('Delete', 'Restore'));
        }
    }

    function deleteBlank(e) {
        e.preventDefault();
        $(this).parents('.rubric_edit_row').remove();
    }

    function viewClickHandler(e) {
        e.preventDefault();
        var comp_id = $('select#comp_select option:checked').val();
        if(comp_id != 0) {
            $('#rubric_container').load("{{ route('ajax.rubric.view') }}/" + comp_id);
        } else {
            alert('You must select a competition to view');
        }
    }

    function editClickHandler(e) {
        e.preventDefault();
        var comp_id = $('select#comp_select option:checked').val();
        if(comp_id != 0) {
            $('#rubric_container').load("{{ route('ajax.rubric.edit') }}/" + comp_id);
        } else {
            alert('You must select a competition to edit');
        }
    }

    function addRowClickHandler(e) {
        e.preventDefault();
        var button = $(this);
        var category = button.parents('.rubric_category');
        var lastRow = category.children('.rubric_edit_row').last();
        var url = "{{ route('ajax.rubric.blank_row')  }}/"
                    + button.data('comp-id') + '/'
                    + button.data('cat-id') + '/'
                    + nextId;

        $.get(url, function(data) {
            $(data).insertAfter(lastRow);
            reorder(category);
            nextId++;
            if(category.children('.rubric_edit_row').length > 9) {
                button.remove();
            }
        });
    }

</script>
@endsection

@section('style')
<style>
    .rubric_section_header {
        display: flex;
        color: white;
        background-color: #428BCA;
        font-weight: bold;
        border-radius: 5px;
        line-height: 1.5em;
    }

    .section_title {
        width: 20%;
        padding-left: 10px;
    }

    .number_holder {
        display: flex;
        width: 80%;
    }
    .number_holder div {
        width: 20%;
    }

    .rubric_row {
        display: flex;
        margin-left: 5%;
        width: 95%;
        border-top: 2px solid darkgrey;
        padding: 5px;
    }

    .rubric_edit_row {
        display: flex;
        padding: 5px;
    }

    .rubric_section_header + .rubric_row {
        border: none;
    }
    .rubric_row div,
    .rubric_edit_row div,
    .rubric_edit_row textarea {
        width: 16.66%;
        border: 1px solid darkgrey;
        bottom: 0;
        padding: 5px;
        margin: 2px;
    }

    .rubric_edit_row input {
        text-align: right;
        font-weight: bold;
    }

    div.rubric_movement_controls {
        text-align: center;
        width: 5%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-flow: row wrap;
        border: none;
    }

    .rubric_edit_row div:first-child {
        width: 5%;
        text-align: center;
        border: none;
    }

    .rubric_row div:first-child {
        text-align: right;
        border: none;
        padding-right: 10px;
    }

    .rubric_edit_row.delta {
        background-color: lightgoldenrodyellow;
    }

    .rubric_category_controls {
        padding-left: 20px;
        margin-bottom: 10px;
    }

    div.rubric_element_and_delete {
        display: flex;
        flex-flow: row wrap;
        justify-content: center;
        border: none;
    }

    div.delete {
        background-color: #ffdbe0;
    }

    .delete input,
    .delete textarea {
        text-decoration: line-through;
    }

    .rubric_delete,
    .rubric_delete_blank{
        align-self: self-end;
    }

</style>
@endsection


@section('main')
<div class="row">
    <label for="vid_competition_id">Select Competition</label>
    <div class="form-group col-md-4">
        {{ Form::select('vid_competition_id', $vid_competitions, $competition_id, [ 'class' => 'form-control', 'id' => 'comp_select']) }}
    </div>
    <div class="form-group">
        <button id="view" class="btn btn-primary btn-sm btn-margin pull-left">View</button>
        <button id="edit" class="btn btn-success btn-sm btn-margin pull-left">Edit</button>

        @if(count($dest_competitions))
            <button id="copyTo" class="btn btn-info btn-sm btn-margin pull-left">Copy To</button>
            <div class="form-group col-md-4">
                {{ Form::select('dest_competition_id', $dest_competitions, null, [ 'class' => 'form-control', 'id' => 'dest_select']) }}
            </div>
        @endif
    </div>
    <div class="col-md-6"></div>
</div>
<div id="rubric_container">
@if($competition_id)
    @if($edit)
        @include('admin.rubric.partial.edit', compact('rubric'))
    @else
        @include('admin.rubric.partial.view', compact('rubric'))
    @endif
@endif
</div>


@endsection