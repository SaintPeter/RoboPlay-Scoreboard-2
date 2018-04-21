<style>


</style>
<button id="close_results_button_{{ $video_id }}" class="btn btn-xs btn-info" style="float: right">Close</button>
<table class="table-compact col-md-8 col-md-offset-1 validation_table">
    <thead>
    <tr>
        <th class="col-md-10">Item</th>
        <th class="col-md-2">Result</th>
    </tr>
    </thead>
    <tbody>
    @foreach($results as $result)
        <tr>
            <td class="col-md-10">
                {{ $result['message'] }}
                @if(isset($result['note']))
                    <br>
                    <span class="validation_note">
                     {{ $result[ 'note' ] }}
                    </span>
                @endif
                @if(isset($result['files']))
                    @foreach($result['files'] as $file)
                            <div style="margin-left: 2em;">{{ $file['filename'] }} &mdash; {{ $file['message'] }}</div>
                    @endforeach
                @endif
            </td>
            <td class="col-md-2 validation_status">
                @switch($result['status'])
                    @case('PASS')
                        <span class="text-success">{{ $result['status'] }}</span>
                        @break
                    @case('FAIL')
                        <span class="text-danger">{{ $result['status'] }}</span>
                        @break
                    @case('WARNING')
                        <span class="text-warning">{{ $result['status'] }}</span>
                @endswitch
            </td>
        </tr>

    @endforeach
    </tbody>
</table>
@if($include_javascript)
<script>
    var newHTML = '<span id="video_result_{{ $video_id }}" class="{{ VideoStatus::toClasses($status) }}">{{ VideoStatus::getDescription($status) }}</span>';
    $('#video_result_{{ $video_id }}').replaceWith(newHTML);
    $('#close_results_button_{{ $video_id }}').click(function(e) {
        e.preventDefault();
       $('#validation_results_{{$video_id}}').remove();
    });
</script>
@endif