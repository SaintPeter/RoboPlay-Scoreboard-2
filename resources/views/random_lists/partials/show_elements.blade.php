<table class="table table-condensed">
    <thead>
        <tr>
            <th>D1</th>
            <th>D2</th>
            <th>D3</th>
            <th>D4</th>
            <th>D5</th>
        </tr>
    </thead>
    <tbody>
        @foreach($elements_list as $elements)
        <tr>
            <td>{{ $elements->d1 }}</td>
            <td>{{ $elements->d2 }}</td>
            <td>{{ $elements->d3 }}</td>
            <td>{{ $elements->d4 }}</td>
            <td>{{ $elements->d5 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<button class="btn btn-info btn-margin random_close">Close</button>