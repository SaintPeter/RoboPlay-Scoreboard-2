@extends('layouts.scaffold')


@section('style')
/* Fix margins for nested inline forms */
.form-inline .form-group{
	margin-left: 0;
	margin-right: 0;
}

/* Make nested form things look good */
.form-group .col-md-6 {
	padding-left: 0;
}

.vertical-container {
	display: table;
	width: 100%;
}

.vertical-container > .col-md-1 {
	display: table-cell;
	vertical-align: middle;
	height: 100%;
	float: none;
}

.indent {
	margin-left: 20px;
}
@endsection

@section('head')
	{{ HTML::script('js/jquery.form.min.js') }}
@endsection

@section('script')
<script>
    $(document).on('ready', dropdownInit);

    var divList = @json($division_list);
    var invoiceList = @json($invoice_list);

    function dropdownInit() {
        $('#year').on('change', populateDropDowns);
        if($('#year').val() != "") {
            $('#year').change();
        }
        $("#invoice_id").on('change', updateTeacherId)
    }

    function populateDropDowns() {
        var year = $('#year').val();
        var selected_div = '{{ request()->old('division_id') }}';
        var divOptions = '<option value="0">- Select Division -</option>';

        for(var cat in divList[year]) {
            divOptions += '<optgroup label="' + cat + '">';
            for(var div_id in divList[year][cat]) {
                var selected = (div_id == selected_div) ? 'selected="selected"' : '';
                divOptions += '<option value="' + div_id + '" ' + selected + '>' + divList[year][cat][div_id] + '</option>';
            }
            divOptions += '</optgroup>';
        }
        $('#division_id').html(divOptions);

        var selected_invoice = '{{ request()->old('invoice_id') }}';
        var invoiceOptions = '<option value="0">- Select Teacher -</option>';
        for(var invoice_id in invoiceList[year]) {
            invoice_id_actual = invoice_id.substr(1);
            var selected = (invoice_id_actual == selected_invoice) ? 'selected="selected"' : '';
            invoiceOptions += '<option value="' + invoice_id_actual + '" ' + selected + '>' + invoiceList[year][invoice_id]['teacher'] + '</option>';
        }
        $('#invoice_id').html(invoiceOptions);

        updateTeacherId();
    }

    function updateTeacherId() {
        var year = $('#year').val();
        var invoice_id = $('#invoice_id').val();
        if(year && invoice_id && invoice_id != 0) {
            invoice_id = "a" + invoice_id;
            $('#teacher_id').val(invoiceList[year][invoice_id]['teacher_id']);
        }
    }
</script>
@endsection

@include('students.partial.js', [ 'type' => 'teams', 'use_teacher_id' => true ])


@section('main')
{!! Form::open(array('route' => 'teams.store', 'role'=>"form", 'class' => 'col-md-6'))  !!}
    <input type="hidden" name="teacher_id" id="teacher_id" />
    <div class="form-group">
        {!! Form::label('name', 'Team Name:')  !!}
        {!! Form::text('name','', array('class'=>'form-control col-md-4'))  !!}
    </div>

    <div class="row">
        <div class="form-group col-md-3">
            {!! Form::label('year', 'Year:') !!}
            {!! Form::select('year', $yearList, null, [ 'class' => 'form-control col-md-2', 'id' => 'year']) !!}
        </div>

        <div class="form-group col-md-9">
            <label for="invoice_id">Teacher:</label>
            <select class="form-control" name="invoice_id" id="invoice_id">
            </select>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('division_id', 'Division:')  !!}
        <select class="form-control" name="division_id" id="division_id">
        </select>
    </div>

    @include('students.partial.fields', [ 'students' => $students ])

	<div class="form-group">
	    {!! Form::label('audit', 'Audit Status:')  !!}
	    {!! Form::select('audit', [ 0 => 'Unchecked', 1 => 'Checked' ], null, [ 'class' => 'form-control col-md-4' ])  !!}
	</div>

 		{!! Form::submit('Submit', array('class' => 'btn btn-primary '))  !!}
 		{{ link_to_route('teams.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

{!! Form::close()  !!}

@include('students.partial.dialogs', compact('index'))

@if ($errors->any())
<div class="col-md-6">
	<h3>Validation Errors</h3>
	<ul>
		{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
	</ul>
</div>
@endif

@endsection


