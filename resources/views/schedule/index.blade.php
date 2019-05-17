@extends('layouts.scaffold')

@section('head')
	{{ HTML::style('css/bootstrap-timepicker.min.css') }}
	{{ HTML::script('js/bootstrap-timepicker.min.js') }}
    {{ HTML::script('js/moment.min.js') }}
@endsection

@section('style')
<style>
    .timepicker {
        width: 120px;
    }

</style>
@endsection

@section('script')
<script>
    var new_index = 99999;

    $(document).on('ready', function() {
        $('.timepicker').timepicker( { minuteStep: 1 });

        $(document).on('click', ".insert_above", insert_above);
        $(document).on('click', ".insert_below", insert_below);

        function insert_above(e) {
            e.preventDefault();
            var button = $(this);
            var row = button.parents('tr');
            var old_index = row.data('id');
            var new_row = row.clone();
            var replace_id = new RegExp('(\\D)' + old_index + '(\\D)',"g");
            new_row.html(new_row.html().replace(replace_id, '$1' + new_index + '$2'));
            new_row.attr('data-id', new_index);
            row.before(new_row);
            $('.timepicker').timepicker({ minuteStep: 1 });
            new_index++;
        }

        function insert_below(e) {
            e.preventDefault();
            var button = $(this);
            var row = button.parents('tr');
            var old_index = row.data('id');
            var new_row = row.clone();
            var replace_id = new RegExp('(\\D)' + old_index + '(\\D)',"g");
            new_row.html(new_row.html().replace(replace_id, '$1' + new_index + '$2'));
            new_row.attr('data-id', new_index);
            row.after(new_row);
            $('.timepicker').timepicker({ minuteStep: 1 });
            new_index++;
        }

    });
</script>
@endsection


@section('main')
{!! Form::open([ 'route' => 'schedule.update', 'method' => 'post' ])  !!}
<table class="table table-striped table-nonfluid table-condensed">
    <tr>
        <th>Start</th>
        <th>Event</th>
        <th>Action</th>
    </tr>
    <?php
        $keys = $schedule->pluck('id')->all();
        $first_key = $keys[0];
        $last_key = $keys[count($keys) - 1];
    ?>
    @foreach($schedule as $row)
    <tr data-id="{{$row->id}}">
        <td>
            <input type="hidden" name="{{ "schedule[{$row->id}][id]" }}" value="{{ $row->id }}">
            <div class="input-group bootstrap-timepicker timepicker">
                <input name="{{ "schedule[{$row->id}][start]" }}" type="text" class="form-control input-sm timepicker" value="{{ $row->start }}">
                <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
            </div>
        </td>
        <td>
            {!! Form::text("schedule[{$row->id}][display]", $row->display, [ 'class' => 'form-control input-sm' ]) !!}
            @if($errors->any())
            <br>
            <ul>
                @foreach($errors as $error)
                <li>{{ $error->message }}</li>
                @endforeach
            </ul>
            @endif
        </td>
        <td>
            @if($row->id != $first_key)
                <button class="btn btn-default btn-sm btn-margin insert_above" title="Insert Row Above">
                    <i class="fa fa-plus"></i>
                    <i class="fa fa-caret-up"></i>
                </button>
            @endif
            @if($row->id != $last_key)
            <button class="btn btn-default btn-sm btn-margin insert_below" title="Insert Row Below">
                <i class="fa fa-plus"></i>
                <i class="fa fa-caret-down"></i>
            </button>
            @endif
        </td>
    </tr>
    @if(isset($row['errors']))
    <tr>
        <div class="col-md-6">
        	<ul>
        		{{ implode('', $row['errors'],('<li class="error">:message</li>')) }}
        	</ul>
        </div>

    </tr>
    @endif
    @endforeach
</table>
    <input type="submit" name="submit" value="Save" class="btn btn-primary">
{!! Form::close()  !!}
@endsection