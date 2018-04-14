@extends('layouts.scaffold')

@section('script')
<script>
$(document).on('ready', function() {
    $(document).on('click', '.add_button', add_type);
    $(document).on('click', '.save_button', save_type);
    $(document).on('click', '.edit_button', edit_type);
    $(document).on('click', '.update_button', update_type);
    $(document).on('click', '.cancel_update_button', cancel_update_type);
    $(document).on('click', '.submit_button', save_type);
    $(document).on('click', '.cancel_button', cancel_type);
});

function add_type(e) {
    e.preventDefault();
    var type = $(this).data('type');
    $.get("{{ route('filetypes.add') }}/" + type, function(data) {
        $("#insert_" + type).before(data);
    });
}

function save_type(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var formdata = $('#add_form_' + id).serialize();
    $.post("{{ route('filetypes.store') }}", formdata, function(data) {
        $("#type_add_row_" + id).replaceWith(data);
    });
}

function edit_type(e) {
    e.preventDefault();
    var type = $(this).data('id');
    var target = $(this).data('target');
    $.get(target, function(data) {
        $("#row_" + type).replaceWith(data);
    });
}

function update_type(e) {
    e.preventDefault();
    var id = $(this).data('id');
    var target = $("#edit_form_" + id).attr('action');
    var formdata = $('#edit_form_' + id).serialize();
    $.post(target, formdata, function(data) {
        $("#type_edit_row_" + id).replaceWith(data);
    });
}

function cancel_update_type(e) {
    e.preventDefault();
    var type = $(this).data('id');
    var target = $(this).data('target');
    $.get(target, function(data) {
        $("#type_edit_row_" + type).replaceWith(data);
    });
}

function cancel_type(e) {
    e.preventDefault();
    var id = $(this).data('id');
    $("#type_add_row_" + id).remove();
}
</script>
@endsection

@section('style')
<style>
tr.header td {
    background-color: darkgrey;
    color: white;
}
</style>
@endsection

@section('main')
<table class="table table-striped table-condensed">
    @foreach($filetypes as $cat_name => $types)
        <tr class="header">
            <td colspan="6">
                <strong>{{ $cat_name }}</strong>
                <button class="pull-right btn btn-xs btn-success add_button" data-type="{{ $types['cat'] }}" title="Add {{ $cat_name }}">
                    <span class="glyphicon glyphicon-plus"></span>
                </button>
            </td>
        </tr>
        <tr>
            <th></th>
            <th>Extension</th>
            <th>Language</th>
            <th>Viewer</th>
            <th>Icon</th>
        </tr>
        @foreach($types['types'] as $type)
            @include('filetypes.partial.typerow', compact('type'))
        @endforeach
        <tr style="display: none" id="insert_{{ $types['cat'] }}">
            <td colspan="6"></td>
        </tr>
    @endforeach
@endsection