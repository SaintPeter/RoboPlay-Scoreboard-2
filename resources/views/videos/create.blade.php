@extends('layouts.scaffold')

@section('style')
<style>
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
</style>
@endsection

@section('head')
	{{ HTML::script('js/jquery.form.min.js') }}
@endsection

@section('script')
    <script>
        $(document).on('ready', dropdownInit);

        var divList = @json($vid_divisions);
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
            var selected_div = '{{ request()->old('vid_division_id') }}';
            var divOptions = '<option value="0">- Select Video Division -</option>';

            for(var cat in divList[year]) {
                divOptions += '<optgroup label="' + cat + '">';
                for(var div_id in divList[year][cat]) {
                    var selected = (div_id == selected_div) ? 'selected="selected"' : '';
                    divOptions += '<option value="' + div_id + '" ' + selected + '>' + divList[year][cat][div_id] + '</option>';
                }
                divOptions += '</optgroup>';
            }
            $('#vid_division_id').html(divOptions);

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

@include('students.partial.js', [ 'type' => 'videos', 'use_teacher_id' => true ])

@section('main')
{!! Form::open(array('route' => 'videos.store', 'role'=>"form", 'class' => 'col-md-8' ))  !!}
    <input type="hidden" name="teacher_id" id="teacher_id" />
	<div class="form-group">
		{!! Form::label('name', 'Name:')  !!}
		{!! Form::text('name', null, [ 'class'=>'form-control col-md-4' ])  !!}
	</div>

	<div class="form-group">
		{!! Form::label('yt_code', 'YouTube URL or Code:')  !!}
		{!! Form::text('yt_code', null, [ 'class'=>'form-control col-md-4' ])  !!}
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
        {!! Form::label('Vid_division_id', 'Division:')  !!}
        <select class="form-control" name="vid_division_id" id="vid_division_id">
        </select>
    </div>

	<label>Content Tags</label>
	<div class="indent">
		<div class="checkbox">
			<label>
				{!! Form::hidden('has_story', 0)  !!}
				{!! Form::checkbox('has_story', 1)  !!} Storyline
			</label>
		</div>

		<div class="checkbox">
			<label>
				{!! Form::hidden('has_choreo', 0)  !!}
				{!! Form::checkbox('has_choreo', 1)  !!} Choreography
			</label>
		</div>

		<div class="checkbox">
			<label>
				{!! Form::hidden('has_task', 0)  !!}
				{!! Form::checkbox('has_task', 1)  !!} Interesting Task
			</label>
		</div>

        <div class="checkbox">
            <label>
                {!! Form::hidden('has_theme', 0)  !!}
                {!! Form::checkbox('has_theme',1)  !!} Yearly Theme
            </label>
        </div>

        <div class="checkbox">
            <label>
                {!! Form::hidden('has_custom', 0)  !!}
                {!! Form::checkbox('has_custom',1)  !!} Custom Designed Part
                <sup><a href="#custom">[1]</a></sup>
            </label>
        </div>

        <div class="checkbox">
            <label>
                {!! Form::hidden('has_advanced', 0)  !!}
                {!! Form::checkbox('has_advanced',1)  !!} Advanced Electronics
                <sup><a href="#advanced">[2]</a></sup>
            </label>
        </div>
	</div>
	<label>Attributes</label>
	<div class="indent">
		<div class="checkbox">
			<label>
				{!! Form::hidden('has_code', 0)  !!}
				{!! Form::checkbox('has_code',1)  !!} Has Code
			</label>
		</div>

		<div class="checkbox">
			<label>
				{!! Form::hidden('has_vid', 0)  !!}
				{!! Form::checkbox('has_vid',1)  !!} Has Video
			</label>
		</div>
	</div>

	@include('students.partial.fields', [ 'students' => $students ])

	<div class="form-group">
		{!! Form::label('awards[]', 'Awards')  !!}
		{!! Form::select('awards[]', $awards_list, null, [ 'class'=>'form-control', 'multiple' => 'multiple', 'size' => 7 ])  !!}
	</div>

	<div class="form-group">
	    {!! Form::label('audit', 'Audit Status:')  !!}
	    {!! Form::select('audit', [ 0 => 'Unchecked', 1 => 'Checked' ], null, [ 'class' => 'form-control col-md-4' ])  !!}
	</div>

	<div class="form-group">
		{!! Form::submit('Submit', array('class' => 'btn btn-primary'))  !!}
				&nbsp;
		{{ link_to_route('videos.index', 'Cancel', [], ['class' => 'btn btn-info']) }}

	</div>


{!! Form::close()  !!}

@include('students.partial.dialogs', compact('index'))

@if ($errors->any())
<div class="col-md-4">
	<h3>Validation Errors</h3>
	<ul>
		{!! implode($errors->all('<li class="error">:message</li>')) !!}
	</ul>
</div>
@endif

@endsection


