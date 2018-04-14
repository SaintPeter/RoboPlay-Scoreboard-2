@extends('layouts.scaffold')

@section('script')
<script>
    $(document).ready(function(){
        $('#view').on('click', viewClickHandler);
        $('#edit').on('click', editClickHandler);

    });

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
    .rubric_section_header + .rubric_row {
        border: none;
    }
    .rubric_row div {
        width: 16.66%;
        border: 1px solid darkgrey;
        bottom: 0;
        padding: 5px;
        margin: 2px;
    }

    .rubric_row div:first-child {
        text-align: right;
        border: none;
        padding-right: 10px;
    }
</style>
@endsection


@section('main')
<div class="row">
    <div class="form-group col-md-4">
        <label for="vid_competition_id">Select Competition</label>
        {{ Form::select('vid_competition_id', $vid_competitions, $competition_id, [ 'class' => 'form-control', 'id' => 'comp_select']) }}
        <button id="view" class="btn btn-primary btn-sm btn-margin pull-right">View</button>
        <button id="edit" class="btn btn-success btn-sm btn-margin pull-right">Edit</button>
    </div>
    <div class="col-md-8"></div>
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