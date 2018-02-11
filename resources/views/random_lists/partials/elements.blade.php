<table>
    <tr>
        <th>D1</th>
        <th>D2</th>
        <th>D3</th>
        <th>D4</th>
        <th>D5</th>
    </tr>
    @for($index = 0; $index < count($data); $index++)
    <tr>
        <td>
            {!! Form::text('d1[' . $index . '][d1]', $data[$index]['d1'], [ 'class' => "form-control" ])  !!}
        </td>
        <td>
            {!! Form::text('d2[' . $index . '][d2]', $data[$index]['d2'], [ 'class' => "form-control" ])  !!}
        </td>
        <td>
            {!! Form::text('d3[' . $index . '][d3]', $data[$index]['d3'], [ 'class' => "form-control" ])  !!}
        </td>
        <td>
            {!! Form::text('d4[' . $index . '][d4]', $data[$index]['d4'], [ 'class' => "form-control" ])  !!}
        </td>
        <td>
            {!! Form::text('d5[' . $index . '][d5]', $data[$index]['d5'], [ 'class' => "form-control" ])  !!}
        </td>
    </tr>
    @endfor
    <tr>
        <td colspan=5><button id="btn btn-success btn-sm pull-right"><i class="fa fa-plus"></i></button></td>
    </tr>
</table>