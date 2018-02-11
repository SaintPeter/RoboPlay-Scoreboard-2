<tr id="row_{{ $type['id'] }}">
    <td>&nbsp;</td>
    <td>{{ $type['ext'] }}</td>
    <td>{{ $type['language'] }}</td>
    <td>{{ $type['viewer'] }}</td>
    <td><i class="fa {{ $type['icon'] }}"></i></td>
    <td>
        <button class="btn btn-info btn-xs edit_button" data-id="{{ $type['id'] }}" data-target="{{ route('filetypes.edit', [ $type['id'] ]) }}" title="Edit">
            <span class="glyphicon glyphicon-pencil"></span>
        </button>

    </td>
</tr>