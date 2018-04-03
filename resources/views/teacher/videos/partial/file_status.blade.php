<table>
    <tbody>
    @foreach($results as $result)
        <tr>
            <td>{{ $results['message'] }}</td>
            <td>{{ $results['status'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
