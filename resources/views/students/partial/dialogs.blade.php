
<div id="choose_students_dialog" style="display: none" title="Choose Students">

</div>

<div id="mass_upload_students_dialog" style="display: none" title="Choose Upload File">
Download this <a href="/docs/roboplay_scoreboard_student_upload_template.xlsx" target="_blank">Excel Template</a> and follow the instructions inside to generate a csv file for upload.<br><br>
  {!! Form::open([ 'route' => 'ajax.import_students_csv', 'files' => true, 'id' => 'upload_form' ] )  !!}
	  {!! Form::label('csv_file', 'CSV File', array('id'=>'','class'=>''))  !!}
	  {!! Form::file('csv_file', ['accept' => '.csv' ])  !!}
  <br/>
  <!-- submit buttons -->
  {!! Form::close()  !!}
</div>